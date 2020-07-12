<?php

namespace JamesClark32\DbTinker\Tests;

use JamesClark32\DbTinker\Commands\DbTinkerCommand;
use PHPUnit\Framework\TestCase;

class DbTinkerCommandTest extends TestCase
{
    public function test_db_tinker_console_command()
    {
        $command = app(DbTinkerCommand::class);
        $this->assertIsObject($command);
    }

    public function test_db_tinker_console_command_has_expected_attributes()
    {
        foreach ($this->getCommandAttributes() as $commandAttribute) {
            $this->assertClassHasAttribute($commandAttribute, DbTinkerCommand::class);
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
