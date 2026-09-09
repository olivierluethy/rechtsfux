#!/bin/zsh
cd "$(dirname "$0")/site"
echo "Rechtsfux läuft auf http://localhost:8080  (Admin: http://localhost:8080/wp-admin  ·  admin / Rechtsfux2026!)"
php -S localhost:8080 -t . router.php
