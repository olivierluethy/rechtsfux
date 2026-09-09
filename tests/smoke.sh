#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# Rechtsfux — Smoke-Test
# Prüft, dass alle wichtigen Seiten den erwarteten HTTP-Status liefern.
# Aufruf:  ./tests/smoke.sh [BASE-URL]   (Standard: http://localhost:8080)
# Exit 0 = alles ok, Exit 1 = mindestens ein Fehler.
# ─────────────────────────────────────────────────────────────
set -u
BASE="${1:-http://localhost:8080}"
pass=0
fail=0

check() { # pfad erwarteter-code
  local path="$1" exp="$2" code
  code="$(curl -s -o /dev/null -w '%{http_code}' "$BASE$path")"
  if [ "$code" = "$exp" ]; then
    printf '  \033[32m✓\033[0m %-48s %s\n' "$path" "$code"
    pass=$((pass + 1))
  else
    printf '  \033[31m✗\033[0m %-48s %s (erwartet %s)\n' "$path" "$code" "$exp"
    fail=$((fail + 1))
  fi
}

echo "Smoke-Test gegen $BASE"
echo "— Kernseiten —"
check "/"                                           200
check "/wp-login.php"                               200

echo "— Kategorien —"
check "/kuendigungsschreiben/"                      200
check "/vertraege/"                                 200
check "/vorsorgedokumente/"                         200
check "/musterbriefe/"                              200
check "/tools/"                                     200
check "/ratgeber/"                                  200

echo "— Dokument-Generatoren —"
check "/kuendigungsschreiben/wohnung-kuendigen/"    200
check "/kuendigungsschreiben/arbeitsstelle-kuendigen/" 200
check "/vertraege/arbeitsvertrag/"                  200
check "/vertraege/auto-kaufvertrag/"                200
check "/vertraege/darlehensvertrag/"                200
check "/vertraege/untermietvertrag/"                200
check "/vorsorgedokumente/patientenverfuegung/"     200
check "/musterbriefe/maengelruege/"                 200

echo "— Statische Seiten & Ratgeber —"
check "/ueber-uns/"                                 200
check "/kontakt/"                                   200
check "/impressum/"                                 200
check "/datenschutz/"                               200
check "/agb/"                                       200
check "/elektronische-signatur-schweiz/"            200

echo "— Fehlerseite —"
check "/gibt-es-nicht-xyz/"                         404

echo ""
echo "Ergebnis: ${pass} ok, ${fail} Fehler"
[ "$fail" -eq 0 ] || exit 1
