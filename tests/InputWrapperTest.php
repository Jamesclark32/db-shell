<?php

namespace JamesClark32\LaravelDbShell\Tests;

use JamesClark32\LaravelDbShell\InputWrapper;
use PHPUnit\Framework\TestCase;

class InputWrapperTest extends TestCase
{
    public function test_it_normalized_user_input()
    {
        $inputWrapper = app()->make(InputWrapper::class);

        foreach ($this->getTransformations() as $before => $after) {

            $inputWrapper->setUserInput($before);
            $query = $inputWrapper->getQuery();
            $normalized = $query->getNormalizedQueryText();

            $this->assertEquals($normalized, $after);
        }
    }

    protected function getTransformations(): array
    {
        return [
            'select * from users\g' => 'select * from users',
            'select * from users\G' => 'select * from users',
            'select * from users;' => 'select * from users',
            '\g\G; ' => '',
            '    select * from users\g    ;' => 'select * from users',
            'select * from users' => 'select * from users',
            'select * from a_table_name_which_ends_in_g' => 'select * from a_table_name_which_ends_in_g',
        ];
    }
}