# bookingSystem-clean-architecture

Backend-only booking system scaffold using PHP 8.4, Symfony 7, MySQL, and Docker.

## Local domain

Add to `/etc/hosts`:

```
127.0.0.1 bookingsystem-dev.de
```

Nginx `server_name` is set to `bookingsystem-dev.de`.

## Quick start

```bash
cp .env.example .env
./infrastructure/dev.sh install
```

API: http://bookingsystem-dev.de:18080 (see `.env` for `APP_PORT` and `APP_DOMAIN`)

## Structure

```
bookingSystem-clean-architecture/
├── backend/              # Symfony application (clean architecture layers TBD)
├── infrastructure/       # Docker, nginx, dev.sh
│   └── dev.sh
├── .env.example
└── SETUP_REPORT.md       # Full setup log and commands
```

## Dev commands

| Command | Description |
|---------|-------------|
| `./infrastructure/dev.sh install` | First-time setup |
| `./infrastructure/dev.sh up` | Start containers |
| `./infrastructure/dev.sh down` | Stop containers |
| `./infrastructure/dev.sh console <cmd>` | Symfony console |
| `./infrastructure/dev.sh composer <cmd>` | Composer in container |

Frontend will be added in a later phase.
