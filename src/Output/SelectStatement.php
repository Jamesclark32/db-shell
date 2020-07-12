<?php

namespace JamesClark32\DbTinker\Output;

use Illuminate\Support\Arr;
use JamesClark32\DbTinker\Output\SelectStatement\Table;
use JamesClark32\DbTinker\Output\SelectStatement\Vertical;
use Symfony\Component\Console\Terminal;

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

    //@TODO: This is an aspect of the query and should be calculated a class representing it rather than here.
    //@TODO: please refactor me
    protected function getIsShowTable(): bool
    {

        //@TODO: Calculate longest record for each row and sum the largest of all. account for spacing. compare to width.
        //       then incorporate this width calculation into getIsShowTable operation

        if (preg_match('~\\\g\s*$~', $this->query->getQueryText())) {
            return true;
        }

        if (preg_match('~\\\G\s*$~', $this->query->getQueryText())) {
            return false;
        }

        if (config('db-tinker.automatically_switch_to_table_display', false) === true) {
            //@TODO: Expensive loop over all results here. Combine with any other loops.
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

            $rowLength = array_sum($maxLengths) + (count($maxLengths) * 3) + 1;

            //inconsistent updating depending on terminal :/ drop to shell execution of stty as workaround for now
            //        $width = app(Terminal::class)->getWidth();
            [$height, $width] = explode(' ', exec('stty size'));


            if ((int)$width < $rowLength) {
                return false;
            }
        }
        return true;
    }
}