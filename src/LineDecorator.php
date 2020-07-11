<?php

namespace JamesClark32\DbTinker;

class LineDecorator
{
    public function getDecoratedLine(string $text, string $color): string
    {
        return $this->getFormatStringOpen($color) . $text . $this->getFormatStringClose();
    }

    public function getBoldDecoratedLine(string $text, string $color): string
    {
        return $this->getFormatStringOpen($color, true) . $text . $this->getFormatStringClose();
    }

    protected function getFormatStringOpen(string $color, $isBold = false): string
    {
        if ($isBold) {
            return '<fg=' . $color . ';options=bold>';
        }
        return '<fg=' . $color . '>';
    }

    protected function getFormatStringClose(): string
    {
        return '</>';
    }
}
