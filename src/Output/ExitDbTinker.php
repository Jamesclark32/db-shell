<?php

namespace JamesClark32\LaravelDbShell\Output;

class ExitLaravelDbShell extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-shell::output.exit');

        $outputTextColor = config('db-shell.colors.responses.exit', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
    }
}