<?php

namespace JamesClark32\DbShell\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JamesClark32\DbShell\DbWrapper;
use JamesClark32\DbShell\History;
use JamesClark32\DbShell\InputWrapper;
use JamesClark32\DbShell\OutputWrapper;
use JamesClark32\DbShell\Query;

class DbShellCommand extends Command
{
    protected $description = 'Launches the user into an interactive database shell';
    protected $signature = 'db:shell';
    protected DbWrapper $dbWrapper;
    protected InputWrapper $inputWrapper;
    protected OutputWrapper $outputWrapper;
    protected Query $query;
    protected History $history;
    protected string $connection;
    protected int $signalCount = 0;

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
        $this->initializeDbShellCommand();

        $this->output->writeln(trans('db-shell::output.startup'));
        $this->output->writeln(trans('db-shell::output.startup_exit'));
        $this->output->newLine();

        $this->testConnection();

        while (true) {
            $this->handleIteration();
        }
    }

    protected function initializeDbShellCommand(): void
    {
        ini_set('memory_limit', '1G');

        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, [$this, 'handelSignal']);
        pcntl_signal(SIGINT, [$this, 'handelSignal']);

        $this->connection = DB::getDefaultConnection();
        $this->outputWrapper->setOutput($this->output);
        $this->history->loadHistory();
        $this->inputWrapper->setHistory($this->history);
    }

    //@TODO: migrate this to input project
    public function handelSignal(int $signalNumber, $signalInformation)
    {
        $this->signalCount += 1;

        if ($this->signalCount > 10) {
            $this->output->writeln(trans('db-shell::output.startup_exit'));
            $this->signalCount = 0;
        }
    }

    protected function handleIteration(): void
    {
        $queries = $this->inputWrapper->setConnectionName($this->connection)->getUserInput();

        foreach ($queries as $query) {
            $this->query = $query;

            $this->reconnectIfShould();

            if ($this->query->getNormalizedQueryText()) {
                if ($this->query->getNormalizedQueryText() === 'exit') {
                    $this->outputWrapper->outputExit();

                    exit;
                }

                if ($this->query->getQueryType() === 'connections') {
                    $connections = config('database.connections');
                    foreach ($connections as $connectionName => $connection) {
                        $tableHeadings = [
                            'Connection Name',
                            'Hostname',
                            'Port',
                            'Username',
                            'Database',
                        ];

                        $tableData[] = [
                            'connectionName' => $connectionName,
                            'hostname' => $connection['host'],
                            'port' => $connection['port'],
                            'username' => $connection['username'],
                            'database' => $connection['database'],
                        ];
                    }

                    $this->output->table($tableHeadings, $tableData);

                    $newConnectionName = $this->anticipate('Which "Connection Name" would you like to use?',
                        array_keys($connections));
                    $this->connection = $newConnectionName;

                    return;
                }

                if ($this->query->getQueryType() === 'connection') {
                    $this->connection = substr($this->query->getNormalizedQueryText(), 11);
                    $this->dbWrapper->setConnection($this->connection);
                    $this->output->writeln(trans('db-shell::output.connections.switch', [
                        'connectionName' => $this->connection,
                    ]));

                    return;
                }

                $this->processQuery();
            }
        }
    }

    protected function reconnectIfShould(): void
    {
        try {
            DB::select('SELECT 1');
        } catch (\Exception $e) {
            $this->outputWrapper->outputReconnecting();
            DB::reconnect();
        }

        $this->testConnection();
    }

    protected function processQuery(): void
    {
        $results = $this->dbWrapper->setQuery($this->query)->execute();
        if (! $results) {
            $results = [];
        }

        $this->outputWrapper
            ->setOutput($this->output)
            ->setProcessingTime($this->dbWrapper->getProcessingTime())
            ->setQuery($this->query)
            ->setResults($results);

        $this->outputWrapper->render();
    }

    protected function testConnection(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->outputWrapper->setResults(
                [
                    'error' => [
                        'errorCode' => $e->getCode(),
                        'errorNumber' => $e->getCode(),
                        'errorMessage' => $e->getMessage(),
                    ],
                ]
            )->outputError();

            $this->output->warning(trans('db-shell::output.connection_error'));
            exit;
        }
    }
}
