<?php

namespace JamesClark32\DbShell\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use JamesClark32\DbShell\Commands\DbShellCommand;

class DbShellServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setLocaleIfShould();

        $this->overwriteDatabaseConfig();

        $this->loadTranslationsFrom(__DIR__.'/../lang/', 'db-shell');

        $this->publishes([
            __DIR__.'/../config/db-shell.php' => config_path('db-shell.php'),
            __DIR__.'/../lang/' => resource_path('lang/vendor/db-shell'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                DbShellCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/db-shell.php', 'db-shell');
    }

    protected function overwriteDatabaseConfig(): void
    {
        $this->getPasswordIfShould();

        foreach ($this->getDatabaseVariableNames() as $databaseVariableName) {
            $value = config('db-shell.connection.'.$databaseVariableName, null);
            if ($value) {
                $this->setDatabaseConfig($databaseVariableName, $value);
            }
        }

        DB::reconnect();
    }

    protected function setLocaleIfShould(): void
    {
        $configuredLocale = config('db-shell.locale', null);
        $availableLanguagePacks = $this->getAvailableLanguagePacks();
        if (in_array($configuredLocale, $availableLanguagePacks)) {
            App::setLocale($configuredLocale);
        }
    }

    protected function getAvailableLanguagePacks(): array
    {
        return array_diff(scandir(__DIR__.'/../lang/'), ['.', '..']);
    }

    protected function setDatabaseConfig(string $configName, string $value): void
    {
        Config::set('database.connections.'.config('database.default').'.'.$configName, $value);
    }

    protected function getDatabaseVariableNames(): array
    {
        return [
            'host',
            'port',
            'username',
            'password',
            'database',
            'socket',
        ];
    }

    protected function getPasswordIfShould(): void
    {
        if (config('db-shell.prompt_for_password', false) === true) {
            echo trans('db-shell::input_prompts.password', [
                'username' => config('db-shell.connection.username'),
                'host' => config('db-shell.connection.host'),
                'database' => config('db-shell.connection.database'),
            ]);

            $file = popen('read -s; echo $REPLY', 'r');
            $password = fgets($file, 100);
            pclose($file);

            Config::set('database.connections.'.config('database.default').'.password', $password);
        }
    }
}
