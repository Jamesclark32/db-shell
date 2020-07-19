<?php

namespace JamesClark32\DbShell\Output;

use Illuminate\Support\Arr;

class Count extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $outputText = trans('db-shell::output.count', [
            'count' => Arr::get($this->results, 'count', 0),
            'seconds' => round($this->processingTime, 2),
        ]);

        $outputTextColor = config('db-shell.colors.responses.count', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);
        $this->outputStyle->newLine();
    }
}
