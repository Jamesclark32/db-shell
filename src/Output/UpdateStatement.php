<?php

namespace JamesClark32\DbTinker\Output;

use Illuminate\Support\Arr;

class UpdateStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-tinker::output.update_response', [
            'count' => Arr::get($this->results, 'count', 0),
            'seconds' => round($this->processingTime, 2),
        ]);
        $outputTextColor = config('db-tinker.colors.responses.update', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}