<?php

namespace JamesClark32\DbTinker\Output;

use Illuminate\Support\Arr;

class SqlError extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-tinker::output.error', [
            'errorCode' => Arr::get($this->results, 'errorCode'),
            'errorNumber' => Arr::get($this->results, 'errorNumber'),
            'errorMessage' => Arr::get($this->results, 'errorMessage', trans('db-tinker.no_error')),
        ]);

        $outputTextColor = 'white';//@TODO: load color from config
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}