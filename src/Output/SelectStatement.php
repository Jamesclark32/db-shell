<?php

namespace JamesClark32\DbTinker\Output;

use Illuminate\Support\Arr;
use JamesClark32\DbTinker\Output\SelectStatement\Table;
use JamesClark32\DbTinker\Output\SelectStatement\Vertical;

class SelectStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        if ($this->getIsShowTable()) {
            $this->renderTable();
            return;
        }
        $this->renderVertical();
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

        if (config('db-tinker.automatically_switch_to_table_display', false) === true) {

            $tableWidth = $this->getTableWidth();

            [$height, $width] = explode(' ', exec('stty size'));

            if ((int)$width < $tableWidth) {
                return false;
            }
        }
        return true;
    }

    protected function getTableWidth(): int
    {
        $columns = array_keys(Arr::get($this->results, 0));

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
}