<?php

namespace JamesClark32\DbTinker;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DbWrapper
{
    protected Query $query;
    protected float $processingTime;
    protected ?array $results = [];

    public function execute(): ?array
    {
        $startProcessingTime = microtime(true);
        $this->executeQuery();
        $this->processingTime = microtime(true) - $startProcessingTime;

        return $this->results;
    }

    public function setQuery(Query $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function getResults(): ?array
    {
        return $this->results;
    }

    public function getProcessingTime(): float
    {
        return $this->processingTime;
    }

    protected function executeQuery()
    {
        $executeMethodName = $this->getExecuteQueryMethod($this->query->getQueryType());

        try {
            $this->results = $this->$executeMethodName();
        } catch (\Exception $e) {
            $this->results = $this->processException($e);
            return null;
        }
    }

    protected function executeSelectQuery(): ?array
    {
        return DB::connection()->getPdo()->query($this->query->getNormalizedQueryText())->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function executeCreateQuery(): ?array
    {
        $count = DB::affectingStatement($this->query->getNormalizedQueryText());

        dump($count);
        return [
            'count' => $count,
        ];
    }

    protected function executeUseQuery(): void
    {
        DB::connection()->getPdo()->exec($this->query->getNormalizedQueryText());
    }

    protected function executeInsertQuery(): ?array
    {
        $results = DB::insert($this->query->getNormalizedQueryText());

        return [
            'count' => $results,
        ];
    }

    protected function executeDeleteQuery(): ?array
    {
        $results = DB::delete($this->query->getNormalizedQueryText());

        return [
            'count' => $results,
        ];
    }

    protected function executeUpdateQuery(): ?array
    {
        $results = DB::update($this->query->getNormalizedQueryText());

        return [
            'count' => $results,
        ];
    }

    protected function processException(\Exception $e): ?array
    {
        if (property_exists($e, 'errorInfo')) {

            $errorInfo = $e->errorInfo;

            return [
                'error' => [
                    'errorCode' => Arr::get($errorInfo, 1),
                    'errorNumber' => Arr::get($errorInfo, 0),
                    'errorMessage' => Arr::get($errorInfo, 2),
                ],
            ];
        }
    }

    protected function getExecuteQueryMethod(string $queryType): string
    {
        return Arr::get($this->getExecuteQueryMethodMappings(), $queryType, 'executeSelectQuery');
    }

    protected function getExecuteQueryMethodMappings(): array
    {
        return [
            'create' => 'executeCreateQuery',
            'delete' => 'executeDeleteQuery',
            'insert' => 'executeInsertQuery',
            'select' => 'executeSelectQuery',
            'show' => 'executeSelectQuery',
            'update' => 'executeUpdateQuery',
            'use' => 'executeUseQuery',
        ];
    }
}