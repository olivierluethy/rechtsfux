# ─────────────────────────────────────────────────────────────
# Rechtsfux — lauffähiges Container-Image
# Baut WordPress + SQLite + das Theme via setup.sh und startet den
# PHP-Server. Start:  docker run --rm -p 8080:8080 ghcr.io/olivierluethy/rechtsfux
# ─────────────────────────────────────────────────────────────
FROM php:8.3-cli-bookworm

# Systemwerkzeuge + SQLite-Erweiterung
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl ca-certificates unzip rsync bash libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# WordPress-Core + SQLite-Plugin beschaffen, installieren und Inhalte seeden.
RUN bash setup.sh

EXPOSE 8080

# Server auf allen Interfaces (im Container) starten.
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app/site", "/app/site/router.php"]
