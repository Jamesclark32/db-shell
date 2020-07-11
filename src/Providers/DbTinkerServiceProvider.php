<?php

namespace JamesClark32\DbTinker\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use JamesClark32\DbTinker\Commands\DbTinkerCommand;

class DbTinkerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setLocaleIfShould();

        $this->overwriteDatabaseConfig();

        $this->loadTranslationsFrom(__DIR__ . '/../lang/', 'db-tinker');

        $this->publishes([
            __DIR__ . '/../config/db-tinker.php' => config_path('db-tinker.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                DbTinkerCommand::class,
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
        $this->mergeConfigFrom(__DIR__ . '/../config/db-tinker.php', 'db-tinker');
    }

    protected function overwriteDatabaseConfig(): void
    {
        $this->getPasswordIfShould();

        foreach ($this->getDatabaseVariableNames() as $databaseVariableName) {
            $value = config('db-tinker.connection.' . $databaseVariableName, null);
            if ($value) {
                $this->setDatabaseConfig($databaseVariableName, $value);
            }
        }

        DB::reconnect();
    }

    protected function setLocaleIfShould(): void
    {
        $configuredLocale = config('db-tinker.locale', null);
        $availableLanguagePacks = $this->getAvailableLanguagePacks();
        if (in_array($configuredLocale, $availableLanguagePacks)) {
            App::setLocale($configuredLocale);
        }
    }

    protected function getAvailableLanguagePacks(): array
    {
        return array_diff(scandir(__DIR__ . '/../lang/'), ['.', '..']);
    }

    protected function setDatabaseConfig(string $configName, string $value): void
    {
        Config::set('database.connections.' . config('database.default') . '.' . $configName, $value);
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
        if (config('db-tinker.prompt_for_password', false) === true) {

            echo trans('db-tinker::input_prompts.password', [
                'username' => config('db-tinker.connection.username'),
                'host' => config('db-tinker.connection.host'),
                'database' => config('db-tinker.connection.database'),
            ]);

            $file = popen("read -s; echo \$REPLY", "r");
            $password = fgets($file, 100);
            pclose($file);

            Config::set('database.connections.' . config('database.default') . '.password', $password);
        }
    }
}