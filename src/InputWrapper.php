<?php

namespace JamesClark32\DbShell;

class InputWrapper
{
    protected ?string $connectionName = null;
    protected History $history;
    protected Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getUserInput(): array
    {
        $userInput = $this->fetchUserInput();
        $userInputArray = preg_split('/(;|\\\g)/i', $userInput);

        $queries = [];
        foreach ($userInputArray as $queryText) {
            $query = new Query();
            $query->setQueryText($queryText);
            $queries[] = $query;
        }

        $this->history->addQueryToHistoryIfShould($userInput);

        return $queries;
    }

    public function setUserInput(string $userInput): void
    {
        $this->query->setQueryText($userInput);
    }

    public function setConnectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;
        return $this;
    }

    public function setHistory(History $history): self
    {
        $this->history = $history;
        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    protected function fetchUserInput(): string
    {
        return readline($this->buildInputPrompt());
    }

    protected function buildInputPrompt(): string
    {
        return trans('db-shell::input_prompts.input_query', [
            'connection' => $this->connectionName,
        ]);
    }
}