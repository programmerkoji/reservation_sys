# Repository Guidelines

## Project Structure & Module Organization

- `docker/`: Docker build context (PHP-FPM image, Nginx config, PHP ini).
- `docker-compose.yml`: Local dev stack (Nginx + PHP-FPM + MySQL + phpMyAdmin).
- `src/`: Laravel application root.
  - `src/app/`, `src/routes/`, `src/resources/`, `src/config/`, `src/database/`: core app code and configuration.
  - `src/public/`: web root (served by Nginx); built assets land in `src/public/build/`.
  - `src/tests/`: PHPUnit tests (`Feature/` and `Unit/`).

## Build, Test, and Development Commands

Run from repository root unless noted.

- Start/stop stack: `docker compose up -d --build` / `docker compose down`
- Tail logs: `docker compose logs -f --tail=100`
- Run Artisan inside container: `docker compose exec backend php artisan <command>`
- Install PHP deps (container): `docker compose exec backend composer install`
- Frontend (run in `src/`): `npm install`, `npm run dev`, `npm run build` (Vite + Tailwind)
- One-shot bootstrap (run in `src/`): `composer run setup` (installs deps, creates `.env`, migrates, builds assets)

## Coding Style & Naming Conventions

- Indentation: 4 spaces (see `src/.editorconfig`); YAML uses 2 spaces.
- PHP: follow Laravel conventions and PSR-12; format with Pint: `cd src && ./vendor/bin/pint`
- Naming: classes `StudlyCase`, methods/vars `camelCase`, routes/controller files match their class names.

## Testing Guidelines

- Framework: PHPUnit via Laravel test runner.
- Run tests: `docker compose exec backend php artisan test`
- Add tests under `src/tests/Feature` for HTTP flows and `src/tests/Unit` for isolated logic; name files `*Test.php`.

## Commit & Pull Request Guidelines

- This repo snapshot doesn’t include `.git`, so no house commit style is detectable; prefer Conventional Commits:
  - Example: `feat(reservations): add cancel endpoint`
- PRs: include a clear summary, test instructions/outputs, and screenshots for UI changes; link related issues/tickets.

## Security & Configuration Tips

- Never commit secrets: use `src/.env.example` as the template; keep `src/.env` local.
- Avoid committing generated deps/artifacts (`src/vendor/`, `src/node_modules/`, `src/public/build/`).
