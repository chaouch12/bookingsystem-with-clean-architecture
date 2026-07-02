# Git Hooks beim Commit

Dieses Projekt nutzt **GrumPHP** über **Docker**, um Code-Qualität automatisch bei jedem `git commit` zu prüfen.

## Ablauf bei `git commit`

```text
git add ...
git commit -m "DEV-2 Meine Änderung"
        │
        ▼
┌─────────────────────────────────────┐
│  1. pre-commit (.githooks/pre-commit) │
│     → PHP CS Fixer (phpcsfixer)       │
│     → PHPStan (phpstan)               │
│     Nur auf gestagte .php-Dateien     │
└─────────────────────────────────────┘
        │ Fehler? → Commit abgebrochen
        ▼
┌─────────────────────────────────────┐
│  2. commit-msg (.githooks/commit-msg) │
│     → Commit-Message-Regeln           │
│     → JIRA-Ticket erforderlich        │
└─────────────────────────────────────┘
        │ Fehler? → Commit abgebrochen
        ▼
   Commit wird erstellt
```

## Was wird geprüft?

| Hook | Task | Was passiert |
|------|------|--------------|
| `pre-commit` | `phpcsfixer` | Code-Style (Spacing, Symfony-Regeln, …) |
| `pre-commit` | `phpstan` | Statische Analyse (Level 6) |
| `commit-msg` | `git_commit_message` | Message muss z. B. `DEV-123 …` enthalten |

Konfiguration: `backend/grumphp.yml`

## Wichtige Dateien

```text
.githooks/
  pre-commit          # Startet GrumPHP pre-commit Tasks
  commit-msg          # Prüft Commit-Message

scripts/
  grumphp-docker.sh   # Führt GrumPHP im PHP-Container aus
  install-git-hooks.sh

backend/
  grumphp.yml         # Tasks & Regeln
  .php-cs-fixer.dist.php
  phpstan.dist.neon
```

## Installation

```bash
# 1. PHP-Container muss laufen
cd infrastructure
docker compose -p booking-system up -d php

# 2. Hooks installieren (einmalig, oder nach composer install)
cd backend
composer run install-git-hooks
```

Das setzt `git config core.hooksPath .githooks` und macht die Skripte ausführbar.

## Wie Docker ins Spiel kommt

Hooks laufen **nicht** mit lokalem PHP, sondern im Container `booking-system-php-1`:

```bash
docker compose -f infrastructure/docker-compose.yml -p booking-system \
  exec -T \
  -e GRUMPHP_GIT_WORKING_DIR=/var/www/project \
  -e GRUMPHP_PROJECT_DIR=/var/www/project/backend \
  -w /var/www/project/backend \
  php php vendor/bin/grumphp ...
```

| Pfad im Container | Bedeutung |
|-------------------|-----------|
| `/var/www/project` | Git-Repo-Root (inkl. `.git`) |
| `/var/www/project/backend` | Symfony/PHP-Backend |

Der Container mountet das komplette Repo, damit GrumPHP auf `.git` und gestagte Dateien zugreifen kann.

## Was passiert bei einem Fehler?

### PHP CS Fixer (z. B. zu viele Leerzeichen)

Commit wird **blockiert**. In der Ausgabe steht ein Fix-Befehl, z. B.:

```bash
docker compose -f infrastructure/docker-compose.yml -p booking-system exec -T \
  -w /var/www/project/backend php php vendor/bin/php-cs-fixer fix \
  src/Presentation/Http/Controller/HealthController.php
```

Danach erneut stagen und committen:

```bash
git add backend/src/...
git commit -m "DEV-2 Meine Änderung"
```

### PHPStan

Commit wird blockiert, wenn statische Analyse-Fehler in den **gestagten** PHP-Dateien gefunden werden.

### Commit-Message

Die Message muss dem Muster entsprechen:

```text
DEV-123 Kurze Beschreibung
```

Regeln (aus `grumphp.yml`):

- JIRA-Nummer: `DEV-1` bis `DEV-999` (Regex: `/DEV{1,3}-\d+/`)
- Subject großgeschrieben
- Kein Punkt am Ende
- Max. 100 Zeichen

## Nur gestagte Dateien

Die Hooks prüfen **nur Dateien, die mit `git add` gestaged wurden**.

- Ungestagede Änderungen werden nicht geprüft.
- Bereits committeter Code ohne neue Änderung wird nicht erneut geprüft.

## Commit-Message Beispiele

```bash
# ✅ OK
git commit -m "DEV-2 Add health endpoint"

# ❌ Fehlt JIRA-Ticket
git commit -m "Add health endpoint"

# ❌ Kleinschreibung am Anfang
git commit -m "DEV-2 add health endpoint"
```

## Manuell testen (ohne Commit)

```bash
# Alle pre-commit Tasks
./scripts/grumphp-docker.sh vendor/bin/grumphp git:pre-commit --no-interaction

# Nur PHP CS Fixer
./scripts/grumphp-docker.sh vendor/bin/grumphp run --tasks=phpcsfixer --no-interaction

# Nur PHPStan
./scripts/grumphp-docker.sh vendor/bin/grumphp run --tasks=phpstan --no-interaction
```

## Häufige Probleme

### `booking-system PHP container is not running`

```bash
cd infrastructure && docker compose -p booking-system up -d php
```

### Hook hängt / reagiert nicht

Ursache war offenes stdin in Docker. Das ist in `scripts/grumphp-docker.sh` mit `< /dev/null` gelöst.

### `grumphp git:init` funktioniert nicht

Dieses Projekt nutzt **eigene Hooks** in `.githooks/`, nicht die von GrumPHP in `.git/hooks/`.

Richtig:

```bash
composer run install-git-hooks
```

Falsch (wird von Git ignoriert):

```bash
vendor/bin/grumphp git:init
```

## Hinweis zu `.php-cs-fixer.dist.php`

```php
->in(__DIR__.'/src')
```

Das ist **korrekt**. `__DIR__` ist bereits `backend/`, der Finder scannt also `backend/src/`.
**Nicht** `backend/src` schreiben — das würde auf `backend/backend/src` zeigen.
