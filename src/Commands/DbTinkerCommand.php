<?php

namespace JamesClark32\DbTinker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use JamesClark32\DbTinker\DbWrapper;
use JamesClark32\DbTinker\History;
use JamesClark32\DbTinker\InputWrapper;
use JamesClark32\DbTinker\OutputWrapper;
use JamesClark32\DbTinker\Query;

class DbTinkerCommand extends Command
{
    protected $description = 'Launches the user into an interactive database shell';
    protected $signature = 'db:tinker';
    protected DbWrapper $dbWrapper;
    protected InputWrapper $inputWrapper;
    protected OutputWrapper $outputWrapper;
    protected Query $query;
    protected History $history;
    protected string $connection;

    public function __construct(
        DbWrapper $dbWrapper,
        History $history,
        InputWrapper $inputWrapper,
        OutputWrapper $outputWrapper
    ) {
        $this->dbWrapper = $dbWrapper;
        $this->history = $history;
        $this->inputWrapper = $inputWrapper;
        $this->outputWrapper = $outputWrapper;

        parent::__construct();
    }

    public function handle(): void
    {
        $this->initializeDbTinkerCommand();

        $this->output->writeln(trans('db-tinker::output.startup'));
        $this->output->newLine();

        while (true) {
            $this->handleIteration();
        }
    }

    protected function initializeDbTinkerCommand(): void
    {
        ini_set('memory_limit', '1G');
        $this->connection = DB::getDefaultConnection();
        $this->outputWrapper->setOutput($this->output);
        $this->history->loadHistory();
        $this->inputWrapper->setHistory($this->history);
    }

    protected function handleIteration(): void
    {
        $this->query = $this->inputWrapper->setConnectionName($this->connection)->getUserInput();

        $this->reconnectIfShould();

        if ($this->query->getNormalizedQueryText()) {

            if ($this->query->getNormalizedQueryText()==='exit'){

                $this->outputWrapper->outputExit();

                exit;
            }

            $this->processQuery();
        }
    }

    protected function reconnectIfShould(): void
    {
        if (!DB::connection()->getDatabaseName()) {
            $this->outputWrapper->outputReconnecting();
            DB::reconnect();
        }
    }

    protected function processQuery(): void
    {
        $results = $this->dbWrapper->setQuery($this->query)->execute();

        $this->outputWrapper
            ->setOutput($this->output)
            ->setProcessingTime($this->dbWrapper->getProcessingTime())
            ->setQuery($this->query);

        if ($results !== null) {
            $this->outputWrapper->setResults($results);
        }

        $this->outputWrapper->render();
    }
}
