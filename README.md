# 予約管理システム（MVP）

小規模事業者向けの「迷わず使える」予約管理システムの最小構成です。

## 技術スタック
- Laravel（最新版）
- 認証：Laravel Breeze（ログイン / ログアウトのみ）
- UI：Blade + Alpine.js
- カレンダー：FullCalendar（CDN）
- DB：MySQL（docker-compose）

## 画面（固定）
- `/login`
- `/dashboard/calendar`（メイン：カレンダー）
- `/dashboard/customers`（顧客管理）
- `/dashboard/settings`（設定）

## データベース
- `customers`：名前 / 電話 / メモ
- `reservations`：開始 / 終了 / 顧客（任意）/ 状態（booked|done|cancel）/ メモ
- `settings`：営業日（曜日）/ 営業時間 / 予約枠（分）

## 起動手順（初回）
1. `.env` 作成：`cp src/.env.example src/.env`
2. コンテナ起動：`docker compose up -d --build`
3. 依存関係：`docker compose exec backend composer install`
4. DB作成：`docker compose exec backend php artisan migrate --seed`
5. フロント：`cd src && npm install && npm run dev`（または `npm run build`）

アクセス：
- アプリ：http://localhost:8000
- phpMyAdmin：http://localhost:8080

## 管理者ログイン
`src/.env` の以下で管理者を作成します（`php artisan db:seed` / `migrate --seed` 実行時）。
- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

## よく使うコマンド
- ログ：`docker compose logs -f --tail=100`
- Artisan：`docker compose exec backend php artisan <command>`
- Composer：`docker compose exec backend composer <command>`

## 開発メモ
- モーダルなどのUIが動かない場合は、Viteが動いていて `@vite(...)` のCSS/JSが読み込めているか確認してください（`cd src && npm run dev`）。
