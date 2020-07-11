<?php


namespace JamesClark32\DbTinker\Output;

class UseStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-tinker::output.database_changed');

        $outputTextColor = config('db-tinker.colors.responses.use', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}