<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCsvUsersFast extends Command
{
    protected $signature = 'check:csv-users-fast {file}';
    protected $description = 'Fast check of CSV IDs using temporary table + LOAD DATA LOCAL INFILE';

    /**
     * CSVのIDがDBに存在するか確認
     * 1億行だと13秒
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Loading CSV into temporary table...");

        DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_csv_ids");

        // 一時テーブル作成
        DB::statement("
            CREATE TEMPORARY TABLE temp_csv_ids (
                id BIGINT NOT NULL
            ) ENGINE=InnoDB
        ");

        // CSV を一時テーブルに読み込み
        $sql = "
            LOAD DATA LOCAL INFILE '{$file}'
            INTO TABLE temp_csv_ids
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            (@id, @name)
            SET id = @id
        ";

        DB::connection()->getPdo()->exec($sql);

        $this->info("CSV loaded. Checking missing IDs...");

        // users に存在しない ID を一括で取得
        $missing = DB::select("
            SELECT t.id
            FROM temp_csv_ids t
            LEFT JOIN users u ON u.id = t.id
            WHERE u.id IS NULL
        ");

        if (empty($missing)) {
            $this->info("No missing IDs! All good.");
            return 0;
        }

        $this->error("Missing IDs found:");
        foreach ($missing as $row) {
            $this->error("ID not found: " . $row->id);
        }

        $this->error("Total missing: " . count($missing));

        return 0;
    }
}
