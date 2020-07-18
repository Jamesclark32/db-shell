<?php

namespace JamesClark32\LaravelDbShell\Tests;

use JamesClark32\LaravelDbShell\Commands\LaravelDbShellCommand;
use PHPUnit\Framework\TestCase;

class LaravelDbShellCommandTest extends TestCase
{
    public function test_db_shell_console_command()
    {
        $command = app(LaravelDbShellCommand::class);
        $this->assertIsObject($command);
    }

    public function test_db_shell_console_command_has_expected_attributes()
    {
        foreach ($this->getCommandAttributes() as $commandAttribute) {
            $this->assertClassHasAttribute($commandAttribute, LaravelDbShellCommand::class);
        }
    }

    protected function getCommandAttributes(): array
    {
        return [
            'description',
            'signature',
            'dbWrapper',
            'inputWrapper',
            'outputWrapper',
            'query',
            'history',
            'connection',
        ];
    }
}
