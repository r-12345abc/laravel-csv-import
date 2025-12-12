<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'import:csv {file} {--table=users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file into MySQL using LOAD DATA INFILE';

    /**
     * CSVを読み込みDBにデータを保存
     * 13分かかった、もっと早いのかも、docker周りの環境の設定が何かおかしい？
     */
    public function handle()
    {
        $file = $this->argument('file');
        $table = $this->option('table');

        $this->info("Importing {$file} into {$table} ...");

        $query = "
            LOAD DATA LOCAL INFILE '{$file}'
            INTO TABLE {$table}
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            (id, name);
        ";

        DB::connection()->getPdo()->exec($query);

        $this->info("Import complete!");
    }
}
