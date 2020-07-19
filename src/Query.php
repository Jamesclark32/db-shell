<?php

namespace JamesClark32\DbShell;

use Illuminate\Support\Arr;

class Query
{
    protected ?string $queryText;
    protected ?string $queryType;
    protected ?string $normalizedQueryText;
    protected bool $hadError = false;

    public function setQueryText(string $queryText): self
    {
        $this->reset();

        $this->queryText = $queryText;

        $this->normalizeQueryText();
        $this->parseType();

        return $this;
    }

    public function getQueryText(): string
    {
        return $this->queryText;
    }

    public function getQueryType(): string
    {
        return $this->queryType;
    }

    public function getNormalizedQueryText(): string
    {
        return $this->normalizedQueryText;
    }

    public function setHadError(bool $hadError): self
    {
        $this->hadError = $hadError;

        return $this;
    }

    public function hadError(): bool
    {
        return $this->hadError;
    }

    protected function normalizeQueryText(): void
    {
        $normalizedQueryText = preg_replace($this->getTrailingSqlCharactersRegex(), '', $this->queryText);
        $this->normalizedQueryText = trim($normalizedQueryText);
    }

    protected function getTrailingSqlCharactersRegex(): string
    {
        return '/(;|\s|\\\g)*$/i';
    }

    protected function parseType(): void
    {
        $words = explode(' ', $this->normalizedQueryText);
        $firstWord = Arr::get($words, 0);
        $this->queryType = strtolower($firstWord);
    }

    protected function reset(): self
    {
        $this->queryText = null;
        $this->normalizedQueryText = null;
        $this->queryType = null;
        $this->hadError = false;

        return $this;
    }
}
