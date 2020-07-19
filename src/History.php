<?php

namespace JamesClark32\DbShell;

use Illuminate\Support\Arr;

class History
{
    protected array $history = [];

    public function loadHistory(): void
    {
        $history = file($this->getHistoryFilePath());

        foreach ($history as $query) {
            readline_add_history(str_replace('\040', ' ', $query));
            $this->history[] = str_replace('\040', ' ', $query);
        }
    }

    public function addQueryToHistoryIfShould(string $query): bool
    {
        if (stripos($query, 'password')) {
            return false;
        }

        if ($this->matchesLastHistoryEntry($query)) {
            return false;
        }

        readline_add_history($query);
        $this->history[] = $query;
        file_put_contents($this->getHistoryFilePath(), PHP_EOL.trim($query), FILE_APPEND);

        return true;
    }

    protected function getHistoryFilePath(): string
    {
        $baseDir = Arr::get($_SERVER, 'HOME');

        return $baseDir.'/.mysql_history';
    }

    protected function matchesLastHistoryEntry(string $query): bool
    {
        return $this->history[count($this->history) - 1] === $query;
    }
}
