<?php

namespace JamesClark32\LaravelDbShell\Output;

use Illuminate\Console\OutputStyle;
use JamesClark32\LaravelDbShell\LineDecorator;
use JamesClark32\LaravelDbShell\Query;

class BaseStatement
{
    protected LineDecorator $lineDecorator;
    protected OutputStyle $outputStyle;
    protected Query $query;
    protected array $results;
    protected float $processingTime;

    public function setLineDecorator(LineDecorator $lineDecorator): self
    {
        $this->lineDecorator = $lineDecorator;
        return $this;
    }

    public function setOutputStyle(OutputStyle $outputStyle): self
    {
        $this->outputStyle = $outputStyle;
        return $this;
    }

    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    public function setQuery(Query $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function setProcessingTime(float $processingTime): self
    {
        $this->processingTime = $processingTime;
        return $this;
    }
}