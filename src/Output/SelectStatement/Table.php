<?php

namespace JamesClark32\DbShell\Output\SelectStatement;

use Illuminate\Support\Arr;
use JamesClark32\DbShell\Output\BaseStatement;
use JamesClark32\DbShell\Output\StatementInterface;
use Symfony\Component\Console\Helper\TableStyle;

class Table extends BaseStatement implements StatementInterface
{
    protected \Symfony\Component\Console\Helper\Table $table;

    public function render(): void
    {
        $this->build();
        $this->table->render();
    }

    protected function build(): void
    {
        $this->table = $this->getTableInstance();

        $this->applyTableStyles();

        $this->setHeaders();
        $this->setRows();
    }

    protected function getTableInstance(): \Symfony\Component\Console\Helper\Table
    {
        return new \Symfony\Component\Console\Helper\Table($this->outputStyle);
    }

    protected function applyTableStyles(): void
    {
        $tableStyler = $this->getTableStyle();
        $this->table->setStyle($tableStyler);
    }

    protected function getTableStyle(): TableStyle
    {
        $tableStyle = app(TableStyle::class);
        $this->getStyles($tableStyle);
        return $tableStyle;
    }

    protected function getStyles(TableStyle $tableStyle): TableStyle
    {
        return $tableStyle
            ->setBorderFormat($this->lineDecorator->getDecoratedLine('%s', config('db-shell.colors.table.border', 'white')))
            ->setCellHeaderFormat($this->lineDecorator->getDecoratedLine('%s', config('db-shell.colors.table.column_head', 'white')))
            ->setCellRowFormat($this->lineDecorator->getDecoratedLine('%s', config('db-shell.colors.table.column_data', 'white')));
    }

    protected function setHeaders(): void
    {
        $firstRow = Arr::get($this->results, 0);
        if ($firstRow) {
            $this->table->setHeaders(array_keys($firstRow));
        }
    }

    protected function setRows(): void
    {
        $this->table->setRows($this->results);
    }
}