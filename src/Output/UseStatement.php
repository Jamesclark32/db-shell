<?php

namespace JamesClark32\DbShell\Output;

class UseStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-shell::output.database_changed');

        $outputTextColor = config('db-shell.colors.responses.use', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}
