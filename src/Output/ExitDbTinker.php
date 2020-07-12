<?php

namespace JamesClark32\DbTinker\Output;

class ExitDbTinker extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-tinker::output.exit');

        $outputTextColor = config('db-tinker.colors.responses.exit', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
    }
}