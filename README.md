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

## Todo:

- [X] Collapse threads
- [ ] Expand images on click
- [ ] Quote replies on click
- [X] Jump to thread/reply on click
- [ ] Preview image before submit
- [ ] Delete threads/replies
- [ ] Ban users by IP
- [ ] Rate limit
- [ ] Admin panel
- [ ] Moderators/janitors
- [ ] Caching
