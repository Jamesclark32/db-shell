<?php

namespace JamesClark32\LaravelDbShell\Output;

use Illuminate\Support\Arr;

class SqlError extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-shell::output.error', [
            'errorCode' => Arr::get($this->results, 'errorCode'),
            'errorNumber' => Arr::get($this->results, 'errorNumber'),
            'errorMessage' => Arr::get($this->results, 'errorMessage', trans('db-shell.no_error')),
        ]);

        $outputTextColor = config('db-shell.colors.responses.error', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}