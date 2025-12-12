# コマンド場所
app/Console/Commands

# メモ
docker-compose exec laravel-csv-import-mysql bash

mysql -uroot -proot

SET PERSIST local_infile= 1; // LOAD DATA LOCAL INFILE 実行時に必要、mysqlで設定できなかったのであきらめて手動

php artisan generate:csv 100000000 // 1億行のcsvファイル作成(デフォルトでusers.csv)
php artisan import:csv users.csv // 1億行のcsvファイルを読み込みDBに保存
php artisan generate:csv 1500000 users150.csv // 150万行のcsvファイル作成
php artisan check:csv-users users150.csv // 150万行のcsvファイルのIDがDBに存在するか確認
php artisan check:csv-users-fast users150.csv // 150万行のcsvファイルのIDがDBに存在するか確認
