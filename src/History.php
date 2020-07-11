<?php

namespace JamesClark32\DbTinker;

use Illuminate\Support\Arr;

class History
{
    public function loadHistory(): void
    {
        $history = file($this->getHistoryFilePath());

        foreach ($history as $query) {
            readline_add_history(str_replace('\040', ' ', $query));
        }
    }

    public function addQueryToHistoryIfShould(string $query): bool
    {
        if (false === stripos($query, 'password')) {
            readline_add_history($query);
            file_put_contents($this->getHistoryFilePath(), PHP_EOL.trim($query), FILE_APPEND);
            return true;
        }
        return false;
    }

    protected function getHistoryFilePath(): string
    {
        $baseDir = Arr::get($_SERVER, 'HOME');
        return $baseDir . '/.mysql_history';
    }
}