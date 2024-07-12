# iboard

## Requirements

- Git
- Docker

## Setting up development environment

- `git clone <repo> && cd <repo>`
- `cp .env.example .env`
- `docker compose -f compose.dev.yml build`
- `docker compose -f compose.dev.yml run workspace composer install`
- `docker compose -f compose.dev.yml run workspace php artisan key:generate`
- `docker compose -f compose.dev.yml run workspace php artisan storage:link`
- `docker compose -f compose.dev.yml run workspace php artisan migrate`
- `docker compose -f compose.dev.yml run workspace php artisan test`
- `docker compose -f compose.dev.yml up`
