<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDataByMysql extends Command
{
    // 1億行だと30分以上かかりそう
    protected $signature = 'generate:big-users {count}';
    protected $description = 'Generate N user records in safe batches';

    public function handle()
    {
        DB::statement("CREATE TABLE seq_10 (N INT);");
        DB::statement("INSERT INTO seq_10 VALUES (0),(1),(2),(3),(4),(5),(6),(7),(8),(9);");

        $count = (int)$this->argument('count');
        $batchSize = 10000000; // 1,000万ずつ生成

        $this->info("Generating {$count} rows in batches of {$batchSize}...");

        for ($offset = 0; $offset < $count; $offset += $batchSize) {

            $limit = min($batchSize, $count - $offset);

            $this->info("Batch starting at {$offset}, generating {$limit} rows");

            DB::statement("
                INSERT INTO users (id, name)
                SELECT seq, CONCAT('name_', seq)
                FROM (
                    SELECT {$offset} + t.n + 1 AS seq
                    FROM (
                        SELECT a.N + b.N * 10 + c.N * 100 + d.N * 1000 +
                               e.N * 10000 + f.N * 100000 +
                               g.N * 1000000 AS N
                        FROM seq_10 a
                        JOIN seq_10 b
                        JOIN seq_10 c
                        JOIN seq_10 d
                        JOIN seq_10 e
                        JOIN seq_10 f
                        JOIN seq_10 g
                        LIMIT {$limit}
                    ) AS t
                ) AS s
            ");
        }

        $this->info("Done!");
    }
}
