<?php

namespace JamesClark32\DbTinker\Output;

use Illuminate\Support\Arr;

class InsertStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-tinker:output.insert_response', [
            'count' => Arr::get($this->results, 'count', 0),
            'seconds' => round($this->processingTime, 2),
        ]);
        $outputTextColor = config('db-tinker.colors.responses.insert', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
    }
}