<?php

namespace JamesClark32\DbTinker;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;
use JamesClark32\DbTinker\Output\DeleteStatement;
use JamesClark32\DbTinker\Output\ExitDbTinker;
use JamesClark32\DbTinker\Output\InsertStatement;
use JamesClark32\DbTinker\Output\SelectStatement;
use JamesClark32\DbTinker\Output\SqlError;
use JamesClark32\DbTinker\Output\UpdateStatement;
use JamesClark32\DbTinker\Output\UseStatement;

class OutputWrapper
{
    protected ?array $results = [];
    protected DeleteStatement $deleteStatement;
    protected ExitDbTinker $exitDbTinker;
    protected float $processingTime;
    protected InsertStatement $insertStatement;
    protected LineDecorator $lineDecorator;
    protected OutputStyle $outputStyle;
    protected Query $query;
    protected SelectStatement $selectStatement;
    protected SqlError $sqlError;
    protected UpdateStatement $updateStatement;
    protected UseStatement $useStatement;

    public function __construct(
        DeleteStatement $deleteStatement,
        ExitDbTinker $exitDbTinker,
        InsertStatement $insertStatement,
        LineDecorator $lineDecorator,
        SelectStatement $selectStatement,
        UpdateStatement $updateStatement,
        UseStatement $useStatement,
        SqlError $sqlError
    ) {
        $this->lineDecorator = $lineDecorator;

        $this->useStatement = $useStatement;
        $this->insertStatement = $insertStatement;
        $this->deleteStatement = $deleteStatement;
        $this->exitDbTinker = $exitDbTinker;
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

        if (!$this->query->hadError()) {
            $this->outputFooter(count($this->results), $this->processingTime);
        }
    }

    protected function getOutputAttribute(string $queryType): string
    {
        return Arr::get($this->getOutputAttributeMappings(), $queryType, 'selectStatement');
    }

    protected function getOutputAttributeMappings(): array
    {
        return [
            'use' => 'useStatement',
            'insert' => 'insertStatement',
            'delete' => 'deleteStatement',
            'update' => 'updateStatement',
            'select' => 'selectStatement',
            'show' => 'selectStatement',
            'create' => 'insertStatement',
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
        $this->exitDbTinker
            ->setLineDecorator($this->lineDecorator)
            ->setOutputStyle($this->outputStyle)
            ->setResults($this->results)
            ->render();
    }

    public function outputReconnecting(): void
    {
        $outputText = trans('db-tinker::output.reconnecting');

        $outputTextColor = $outputTextColor = config('db-tinker.colors.responses.reconnecting', 'white');
        $this->outputStyle->writeln($this->lineDecorator->getDecoratedLine($outputText, $outputTextColor));
        $this->outputStyle->newLine();
    }

    public function setProcessingTime(float $processingTime): self
    {
        $this->processingTime = $processingTime;
        return $this;
    }

    protected function outputFooter(int $count, float $processingTime): void
    {
        $outputText = trans_choice('db-tinker::output.footer', $count, [
            'count' => number_format($count),
            'seconds' => round($processingTime, 2),
        ]);

        $outputTextColor = $outputTextColor = config('db-tinker.colors.responses.footer', 'white');
        $this->outputStyle->writeln($this->lineDecorator->getDecoratedLine($outputText, $outputTextColor));
        $this->outputStyle->newLine();
    }

    protected function fetchConfirmDisplay(int $count): bool
    {
        if ($this->shouldVerifyOutputLargeResultSets() && $this->exceedsLargeResultSetThreshold($count)) {
            return $this->outputStyle->confirm(trans('db-tinker::output.confirm_display', [
                'count' => number_format($count),
            ]));
        }

        return true;
    }

    protected function shouldVerifyOutputLargeResultSets(): bool
    {
        return config('db-tinker.confirm_large_result_set_display');
    }

    protected function exceedsLargeResultSetThreshold(int $count): bool
    {
        return $count > config('db-tinker.confirm_large_result_set_limit');
    }
}