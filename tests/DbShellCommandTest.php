<?php

namespace JamesClark32\DbShell\Tests;

use JamesClark32\DbShell\Commands\DbShellCommand;
use PHPUnit\Framework\TestCase;

class DbShellCommandTest extends TestCase
{
    public function test_db_shell_console_command()
    {
        $command = app(DbShellCommand::class);
        $this->assertIsObject($command);
    }

    public function test_db_shell_console_command_has_expected_attributes()
    {
        foreach ($this->getCommandAttributes() as $commandAttribute) {
            $this->assertClassHasAttribute($commandAttribute, DbShellCommand::class);
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
