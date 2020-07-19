<?php

namespace JamesClark32\DbShell\Tests;

use Illuminate\Support\Facades\DB;
use JamesClark32\DbShell\Providers\DbShellServiceProvider;
use Orchestra\Testbench\TestCase;

class LaravelTestBase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DbShellServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            DB::class,
        ];
    }
}