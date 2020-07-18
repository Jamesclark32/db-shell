<?php

namespace JamesClark32\LaravelDbShell\Output;

interface StatementInterface
{
    public function render(): void;
}