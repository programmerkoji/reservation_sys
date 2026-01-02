# Laravel + Docker (Nginx/PHP-FPM/MySQL)

## 起動手順（初回）

1. `src/.gitkeep` を削除（存在する場合）
   - `rm -f src/.gitkeep`
2. Laravel を `src/` に作成
   - `docker compose run --rm backend composer create-project laravel/laravel .`
3. コンテナ起動
   - `docker compose up -d --build`

## アクセス先

- Laravel: http://localhost:8000
- phpMyAdmin: http://localhost:8080

## DB接続情報

DB接続情報は `src/.env` に書き、`docker-compose.yml` からは参照するようにしています。

初回は以下で作成してください。

- `cp src/.env.example src/.env`

設定項目（例）:

- Laravel: `DB_*`
- MySQL コンテナ: `MYSQL_*`
- phpMyAdmin: `PMA_*`

## よく使うコマンド

- Artisan: `docker compose exec backend php artisan <command>`
- Composer: `docker compose exec backend composer <command>`
- ログ: `docker compose logs -f --tail=100`
