<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:csv {count} {--output=users.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate large CSV file using seq + awk';

    /**
     * 1億行だと85秒くらい
     */
    public function handle()
    {
        $count = (int)$this->argument('count');
        $output = $this->option('output');

        $this->info("Generating CSV with {$count} rows...");
        $this->info("Output: {$output}");

        // seq + awk コマンド
        $cmd = "seq {$count} | awk '{print \$1 \",name_\" \$1}' > {$output}";

        $this->info("Executing: {$cmd}");

        // 実行
        exec($cmd, $outputLines, $status);

        if ($status !== 0) {
            $this->error("Failed to generate CSV.");
            return 1;
        }

        $this->info("CSV generated successfully!");
        return 0;
    }
}
