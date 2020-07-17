<?php

namespace JamesClark32\DbTinker\Output\SelectStatement;

use Illuminate\Support\Arr;
use JamesClark32\DbTinker\Output\BaseStatement;
use JamesClark32\DbTinker\Output\StatementInterface;

class Vertical extends BaseStatement implements StatementInterface
{
    protected array $keys;
    protected int $longestKey;

    public function render(): void
    {
        $this->build();
        $this->displayResults();
    }

    public function build(): void
    {
        $this->keys = $this->getKeys();
        $this->calculateLongestKey();
    }

    protected function getKeys(): array
    {
        $firstRow = Arr::get($this->results, 0);
        return array_keys($firstRow);
    }

    protected function calculateLongestKey(): void
    {
        $longestKey = 0;

        foreach ($this->keys as $key) {

            $length = strlen($key);

            if ($length > $longestKey) {
                $longestKey = $length;
            }
        }
        $this->longestKey = $longestKey;
    }

    protected function displayResults(): void
    {
        foreach ($this->results as $rowNumber => $rowData) {
            $this->displayResult($rowNumber, $rowData);
        }
    }

    protected function displayResult(int $rowNumber, array $rowData): void
    {
        $this->outputStyle->writeln($this->getRowDelimiter($rowNumber));

        foreach ($rowData as $key => $value) {
            $this->outputStyle->writeln($this->getAttributeLine($key, $value));
        }
    }

    protected function getRowDelimiter(int $rowNumber): string
    {
        $padding = str_repeat('*', 27);

        $rowNumberText = trans('db-tinker::output.vertical.row_number', [
            'rowNumber' => number_format($rowNumber),
        ]);

        $displayText = $padding . $rowNumberText . $padding;

        $outputTextColor = config('db-tinker.colors.vertical.delimiter_row', 'white');
        return $this->lineDecorator->getDecoratedLine($displayText, $outputTextColor);
    }

    protected function getAttributeLine($key, $value): string
    {
        return $this->getAttributeKey($key) . $this->getAttributeValue($value);
    }

    protected function getAttributeKey($string): string
    {
        $displayText = str_pad($string, $this->getLongestKey(), ' ', STR_PAD_LEFT) . ': ';
        $outputTextColor = config('db-tinker.colors.vertical.column_head', 'white');
        return $this->lineDecorator->getDecoratedLine($displayText, $outputTextColor);
    }

    protected function getAttributeValue($string): string
    {
        $outputTextColor = config('db-tinker.colors.vertical.column_data', 'white');
        return $this->lineDecorator->getDecoratedLine($string, $outputTextColor);
    }

    protected function getLongestKey(): int
    {
        if ($this->longestKey < 1) {
            $this->calculateLongestKey();
        }
        return $this->longestKey;
    }
}