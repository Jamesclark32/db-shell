<?php

namespace JamesClark32\DbShell\Output;

use Illuminate\Support\Arr;
use JamesClark32\DbShell\Output\SelectStatement\Table;
use JamesClark32\DbShell\Output\SelectStatement\Vertical;

class SelectStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        if ($this->getIsShowTable()) {
            $this->renderTable();
            $this->renderFooterIfShould();
            return;
        }
        $this->renderVertical();
        $this->renderFooterIfShould();
    }

    protected function renderTable(): void
    {
        $tableSelectOutput = app(Table::class);
        $tableSelectOutput
            ->setOutputStyle($this->outputStyle)
            ->setProcessingTime($this->processingTime)
            ->setQuery($this->query)
            ->setResults($this->results)
            ->setLineDecorator($this->lineDecorator)
            ->render();
    }

    protected function renderVertical(): void
    {
        $tableSelectOutput = app(Vertical::class);
        $tableSelectOutput
            ->setOutputStyle($this->outputStyle)
            ->setProcessingTime($this->processingTime)
            ->setQuery($this->query)
            ->setResults($this->results)
            ->setLineDecorator($this->lineDecorator)
            ->render();
    }

    protected function getIsShowTable(): bool
    {
        if (preg_match('~\\\g\s*$~', $this->query->getQueryText())) {
            return true;
        }

        if (preg_match('~\\\G\s*$~', $this->query->getQueryText())) {
            return false;
        }

        if (config('db-shell.automatically_switch_to_table_display', false) === true) {

            $tableWidth = $this->getTableWidth();

            [$height, $width] = explode(' ', exec('stty size'));

            if ((int)$width < $tableWidth) {
                return false;
            }
        }
        return true;
    }

    protected function renderFooterIfShould(): void
    {
        if (!$this->query->hadError()) {
            $this->outputFooter(count($this->results), $this->processingTime);
        }
    }

    protected function outputFooter(int $count, float $processingTime): void
    {
        $outputText = trans_choice('db-shell::output.footer', $count, [
            'count' => number_format($count),
            'seconds' => round($processingTime, 2),
        ]);

        $outputTextColor = $outputTextColor = config('db-shell.colors.responses.footer', 'white');
        $this->outputStyle->writeln($this->lineDecorator->getDecoratedLine($outputText, $outputTextColor));
        $this->outputStyle->newLine();
    }

    protected function getTableWidth(): int
    {
        $columns = $this->getColumns();


        $maxLengths = [];
        foreach ($columns as $column) {
            $maxLengths[$column] = strlen($column);
        }

        foreach ($this->results as $row) {
            foreach ($maxLengths as $column => $currentMax) {
                $length = strlen(Arr::get($row, $column));
                if ($length > $currentMax) {
                    Arr::set($maxLengths, $column, $length);
                }
            }
        }

        return array_sum($maxLengths) + (count($maxLengths) * 3) + 1;
    }

    protected function getColumns(): array
    {
        $firstRow = Arr::get($this->results, 0);
        if (is_array($firstRow)) {
            return array_keys($firstRow);
        }
        return [];
    }
}