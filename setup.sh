#!/bin/zsh
# ─────────────────────────────────────────────────────────────
# Rechtsfux — Setup
# Beschafft WordPress-Core + SQLite-Plugin, konfiguriert die
# SQLite-Anbindung, installiert WordPress und seedet alle Inhalte.
# Idempotent: kann mehrfach ausgeführt werden.
#
# Voraussetzungen: php (mit pdo_sqlite), curl, tar, unzip.
# Aufruf:  ./setup.sh   danach:  ./start.sh
# ─────────────────────────────────────────────────────────────
set -e
ROOT="$(cd "$(dirname "$0")" && pwd)"
SITE="$ROOT/site"
ADMIN_USER="admin"
ADMIN_PASS="Rechtsfux2026!"
ADMIN_MAIL="admin@rechtsfux.example"
PORT="8080"

echo "▸ PHP-SQLite prüfen …"
php -m | grep -qi pdo_sqlite || { echo "FEHLER: PHP ohne pdo_sqlite."; exit 1; }

mkdir -p "$SITE"

# 1) WordPress-Core (nur falls fehlt)
if [ ! -f "$SITE/wp-settings.php" ]; then
  echo "▸ WordPress-Core herunterladen …"
  curl -sL -o /tmp/rf-wp.tar.gz https://wordpress.org/latest.tar.gz
  tar -xzf /tmp/rf-wp.tar.gz -C /tmp
  # Core-Dateien nach site/ kopieren, ohne wp-content zu überschreiben
  rsync -a --exclude 'wp-content' /tmp/wordpress/ "$SITE/" 2>/dev/null || \
    (cd /tmp/wordpress && find . -maxdepth 1 -not -name wp-content -not -name . -exec cp -R {} "$SITE/" \;)
  mkdir -p "$SITE/wp-content/plugins" "$SITE/wp-content/themes"
  echo "  Core installiert."
else
  echo "▸ WordPress-Core bereits vorhanden."
fi

# 2) SQLite Database Integration Plugin (nur falls fehlt)
if [ ! -d "$SITE/wp-content/plugins/sqlite-database-integration" ]; then
  echo "▸ SQLite-Plugin herunterladen …"
  curl -sL -o /tmp/rf-sqlite.zip https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip
  unzip -q -o /tmp/rf-sqlite.zip -d "$SITE/wp-content/plugins"
  echo "  SQLite-Plugin installiert."
fi

# 3) db.php Drop-in erzeugen
if [ ! -f "$SITE/wp-content/db.php" ]; then
  echo "▸ SQLite db.php-Drop-in erzeugen …"
  php -r '
    $src = file_get_contents($argv[1]);
    $src = str_replace("{SQLITE_IMPLEMENTATION_FOLDER_PATH}", "/wp-content/plugins/sqlite-database-integration", $src);
    $src = str_replace("{SQLITE_PLUGIN}", "sqlite-database-integration/load.php", $src);
    file_put_contents($argv[2], $src);
  ' "$SITE/wp-content/plugins/sqlite-database-integration/db.copy" "$SITE/wp-content/db.php"
fi
mkdir -p "$SITE/wp-content/database"

# 4) WordPress installieren (nur falls noch nicht installiert)
echo "▸ WordPress installieren (falls nötig) …"
php -r '
  $_SERVER["HTTP_HOST"]="localhost:'"$PORT"'"; $_SERVER["REQUEST_URI"]="/";
  define("WP_INSTALLING", true);
  require $argv[1]."/wp-load.php";
  require ABSPATH."wp-admin/includes/upgrade.php";
  if (is_blog_installed()) { echo "  bereits installiert\n"; exit; }
  $r = wp_install("Rechtsfux — Das clevere Schweizer Vertrags-Werkzeug", "'"$ADMIN_USER"'", "'"$ADMIN_MAIL"'", true, "", "'"$ADMIN_PASS"'");
  echo "  installiert (user_id=".$r["user_id"].")\n";
' "$SITE"

# 5) Theme aktivieren + Inhalte seeden
echo "▸ Theme aktivieren & Inhalte seeden …"
php -r '
  $_SERVER["HTTP_HOST"]="localhost:'"$PORT"'"; $_SERVER["REQUEST_URI"]="/";
  define("WP_USE_THEMES", false);
  require $argv[1]."/wp-load.php";
  switch_theme("rechtsfux");
  if (!function_exists("activate_plugin")) require ABSPATH."wp-admin/includes/plugin.php";
  $p="sqlite-database-integration/load.php";
  if (file_exists(WP_PLUGIN_DIR."/".$p) && is_plugin_inactive($p)) activate_plugin($p);
' "$SITE"
( cd "$SITE" && php rf-seed.php )

echo ""
echo "✅ Fertig. Starten:  ./start.sh"
echo "   Web:    http://localhost:$PORT"
echo "   Login:  http://localhost:$PORT/wp-login.php   ($ADMIN_USER / $ADMIN_PASS)"
