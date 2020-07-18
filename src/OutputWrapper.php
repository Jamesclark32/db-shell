<?php

namespace JamesClark32\LaravelDbShell;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;
use JamesClark32\LaravelDbShell\Output\Count;
use JamesClark32\LaravelDbShell\Output\ExitLaravelDbShell;
use JamesClark32\LaravelDbShell\Output\SelectStatement;
use JamesClark32\LaravelDbShell\Output\SqlError;
use JamesClark32\LaravelDbShell\Output\UpdateStatement;
use JamesClark32\LaravelDbShell\Output\UseStatement;

class OutputWrapper
{
    protected ?array $results = [];
    protected Count $count;
    protected ExitLaravelDbShell $exitLaravelDbShell;
    protected float $processingTime;
    protected LineDecorator $lineDecorator;
    protected OutputStyle $outputStyle;
    protected Query $query;
    protected SelectStatement $selectStatement;
    protected SqlError $sqlError;
    protected UpdateStatement $updateStatement;
    protected UseStatement $useStatement;

    public function __construct(
        Count $count,
        ExitLaravelDbShell $exitLaravelDbShell,
        LineDecorator $lineDecorator,
        SelectStatement $selectStatement,
        UpdateStatement $updateStatement,
        UseStatement $useStatement,
        SqlError $sqlError
    ) {
        $this->lineDecorator = $lineDecorator;
        $this->count = $count;
        $this->useStatement = $useStatement;
        $this->exitLaravelDbShell = $exitLaravelDbShell;
        $this->updateStatement = $updateStatement;
        $this->selectStatement = $selectStatement;
        $this->sqlError = $sqlError;
    }

    public function setOutput(OutputStyle $outputStyle): self
    {
        $this->outputStyle = $outputStyle;
        return $this;
    }

    public function setQuery(Query $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function render(): void
    {
        if (Arr::has($this->results, 'error')) {
            $this->outputError();
            return;
        }

        $outputAttribute = $this->getOutputAttribute($this->query->getQueryType());

        if (!$this->fetchConfirmDisplay(count($this->results))) {
            return;
        }

        $this->$outputAttribute
            ->setLineDecorator($this->lineDecorator)
            ->setOutputStyle($this->outputStyle)
            ->setProcessingTime($this->processingTime)
            ->setQuery($this->query)
            ->setResults($this->results)
            ->render();
    }

    protected function getOutputAttribute(string $queryType): string
    {
        return Arr::get($this->getOutputAttributeMappings(), $queryType, 'selectStatement');
    }

    protected function getOutputAttributeMappings(): array
    {
        return [
            'create' => 'count',
            'delete' => 'count',
            'drop' => 'count',
            'insert' => 'count',
            'select' => 'selectStatement',
            'show' => 'selectStatement',
            'update' => 'updateStatement',
            'use' => 'useStatement',
        ];
    }

    public function setResults(array $results = []): self
    {
        $this->results = $results;
        return $this;
    }

    public function outputError(): void
    {
        $this->sqlError
            ->setLineDecorator($this->lineDecorator)
            ->setOutputStyle($this->outputStyle)
            ->setResults(Arr::get($this->results, 'error'))
            ->render();
    }

    public function outputExit(): void
    {
        $this->exitLaravelDbShell
            ->setLineDecorator($this->lineDecorator)
            ->setOutputStyle($this->outputStyle)
            ->setResults($this->results)
            ->render();
    }

    public function outputReconnecting(): void
    {
        $outputText = trans('db-shell::output.reconnecting');

        $outputTextColor = $outputTextColor = config('db-shell.colors.responses.reconnecting', 'white');
        $this->outputStyle->writeln($this->lineDecorator->getDecoratedLine($outputText, $outputTextColor));
        $this->outputStyle->newLine();
    }

    public function setProcessingTime(float $processingTime): self
    {
        $this->processingTime = $processingTime;
        return $this;
    }

    protected function fetchConfirmDisplay(int $count): bool
    {
        if ($this->shouldVerifyOutputLargeResultSets() && $this->exceedsLargeResultSetThreshold($count)) {
            return $this->outputStyle->confirm(trans('db-shell::output.confirm_display', [
                'count' => number_format($count),
            ]));
        }

        return true;
    }

    protected function shouldVerifyOutputLargeResultSets(): bool
    {
        return config('db-shell.confirm_large_result_set_display');
    }

    protected function exceedsLargeResultSetThreshold(int $count): bool
    {
        return $count > config('db-shell.confirm_large_result_set_limit');
    }
}