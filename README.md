# iboard

## Requirements

- Git
- Docker

## Setting up dev environment

- `git clone <repo> && cd <repo>`
- `cp .env.example .env`
- `docker compose -f compose.dev.yml build`
- `docker compose -f compose.dev.yml up -d workspace`
- `docker compose -f compose.dev.yml exec workspace composer install`
- `docker compose -f compose.dev.yml exec workspace php artisan key:generate`
- `docker compose -f compose.dev.yml exec workspace php artisan storage:link`
- `docker compose -f compose.dev.yml exec workspace php artisan migrate`
- `docker compose -f compose.dev.yml exec workspace php artisan test`
- `docker compose -f compose.dev.yml up -d`
