<?php

namespace JamesClark32\DbTinker\Output;

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

        $width = (new Terminal())->getWidth();
        if ($width < 150) {//@TODO: move to config
            return false;
        }
        return true;
    }
}