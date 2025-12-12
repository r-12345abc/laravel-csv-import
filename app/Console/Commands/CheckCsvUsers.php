<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCsvUsers extends Command
{
    protected $signature = 'check:csv-users {file}';
    protected $description = 'Check CSV rows against users table ID and show missing ones';

    /**
     * CSVのIDがDBに存在するか確認
     * 1億行だと15分15秒(915秒)
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Checking CSV: {$file}");

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("Could not open file.");
            return 1;
        }

        $lineNumber = 0;
        $errors = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;

            // CSVをカンマで分割(id,name)
            $cols = str_getcsv($line);

            if (!isset($cols[0])) {
                $this->error("Invalid line at {$lineNumber}");
                continue;
            }

            $id = (int)$cols[0];

            // users に存在するか確認（高速クエリ）
            $exists = DB::table('users')->where('id', $id)->exists();

            if (!$exists) {
                $this->error("ID not found in DB at line {$lineNumber}: {$id}");
                $errors++;
            } else {
                $this->info("ID found {$id}");
            }
        }

        fclose($handle);

        $this->info("Check finished. Errors: {$errors}");

        return 0;
    }
}
