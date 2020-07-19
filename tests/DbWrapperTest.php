<?php

namespace JamesClark32\DbShell\Tests;

use Illuminate\Support\Arr;
use JamesClark32\DbShell\DbWrapper;
use JamesClark32\DbShell\Query;

class DbWrapperTest extends LaravelTestBase
{
    public function test_it_executes_a_series_of_queries_without_error()
    {
        $query = new Query();
        $dbWrapper = new DbWrapper();

        foreach ($this->getQueryRoutine() as $queryText) {
            $query->setQueryText($queryText);
            $dbWrapper->setQuery($query);

            $dbWrapper->execute();
            $results = $dbWrapper->getResults();
            $this->assertNull(Arr::get($results, 'error'));
        }
    }

    public function test_it_fails_bad_query_gracefully()
    {
        $query = new Query();
        $dbWrapper = new DbWrapper();

        foreach ($this->getBadQueryRoutine() as $queryText) {
            $query->setQueryText($queryText);
            $dbWrapper->setQuery($query);

            $dbWrapper->execute();
            $results = $dbWrapper->getResults();
            $this->assertNotNull(Arr::get($results, 'error'));
        }
    }

    protected function getQueryRoutine(): array
    {
        $uniqueId = uniqid();

        return [
            'CREATE TABLE `table_'.$uniqueId.'` (first_name VARCHAR(255), last_name VARCHAR(255))',
            'INSERT INTO `table_'.$uniqueId.'` (first_name, last_name) values ("James", "Clark")',
            'SELECT * FROM `table_'.$uniqueId.'`',
            'INSERT INTO `table_'.$uniqueId.'` (first_name, last_name) values ("John", "Smith")',
            'UPDATE `table_'.$uniqueId.'` SET first_name = "Joe" where first_name="John"',
            'DELETE FROM `table_'.$uniqueId.'` WHERE first_name = "Joe"',
            'SELECT * FROM `table_'.$uniqueId.'`',
            'SELECT * FROM `table_'.$uniqueId.'`\g',
            'SELECT * FROM `table_'.$uniqueId.'`\G',
            'SELECT DATE(); SELECT * FROM `table_'.$uniqueId.'`;',
            'DROP TABLE `table_'.$uniqueId.'`',
        ];
    }

    protected function getBadQueryRoutine(): array
    {
        return [
            'Hello',
            'This is not valid sql, and will throw an error when executed. Will the error be handled gracefully?',
            'SELECT my heart <3',
        ];
    }
}
