<?php

namespace JamesClark32\DbShell\Output;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateStatement extends BaseStatement implements StatementInterface
{
    public function render(): void
    {
        $count = Arr::get($this->results, 'count', 0);

        $outputText = trans('db-shell::output.count', [
            'count' => $count,
            'seconds' => round($this->processingTime, 2),
        ]);
        $outputTextColor = config('db-shell.colors.responses.update', 'white');
        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);

        $this->outputStyle->writeln($decoratedOutputText);

        $warningsCount = Arr::get(DB::select('SELECT @@warning_count as count'), 0)->count;

        $outputText = trans('db-shell::output.update_response_summary', [
            'matchCount' => $count,
            'changedCount' => $count,
            'warningCount' => $warningsCount,
        ]);

        $decoratedOutputText = $this->lineDecorator->getDecoratedLine($outputText, $outputTextColor);
        $this->outputStyle->writeln($decoratedOutputText);

        $this->outputStyle->newLine();
    }
}