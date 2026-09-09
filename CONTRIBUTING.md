# Mitwirken an Rechtsfux

Danke für dein Interesse! Diese Anleitung fasst zusammen, wie du lokal arbeitest und was
beim Beitragen zu beachten ist. Für den vollständigen Überblick über Aufbau und
Design-Entscheidungen siehe die [README](README.md).

## Lokal starten

```bash
./setup.sh     # WordPress-Core + SQLite beschaffen, installieren, seeden
./start.sh     # Server auf http://localhost:8080
```

Login: **http://localhost:8080/wp-login.php** · `admin` / `Rechtsfux2026!`

## Grundprinzipien

- **`inc/ia.php` ist die einzige Quelle der Wahrheit** für Navigation, Hub-Seiten und Seeding.
  Struktur-Änderungen passieren dort; danach `cd site && php rf-seed.php`.
- **Design lebt im Theme-Code**, nicht im Seiteneditor — für Konsistenz und Responsivität.
- **Keine Build-Tools, keine Frameworks.** Reines PHP + Vanilla-JS + eine handgeschriebene
  `main.css` (Tokens → Komponenten → Responsive). Externe Assets werden vermieden.
- **Dark/Light** über Tokens: jede Farbe hat einen Light- und einen Dark-Wert; nie nur in einem
  Media-Query definieren.
- **Barrierefreiheit** als Grundlinie: semantisches HTML, Fokus sichtbar, Tastaturbedienung,
  `prefers-reduced-motion` respektieren.

## Häufige Aufgaben

**Neuen Dokument-Generator hinzufügen**
1. In `inc/ia.php` das Item auf `'ready' => true` setzen und in `rf_documents()` eintragen
   (`title`, `parent`, `doc`, `lead`).
2. In `inc/doc-forms.php` eine `rf_doc_<typ>()`-Funktion schreiben (Formular + `<mark data-bind>`-Vorschau)
   und im `switch` von `rf_render_document()` verdrahten.
3. `cd site && php rf-seed.php`.

**Neuen Rechner hinzufügen**
1. Karte mit Anker in `templates/tools.php`.
2. Logik-Modul in `assets/js/calculators.js` (Muster wie die bestehenden).
3. Eintrag in `inc/ia.php` unter der Kategorie „Tools" (`tools#<anker>`).

**Menü-/Kategorie-Eintrag ändern:** nur `inc/ia.php`, dann neu seeden.

## Qualität & CI

Vor dem Commit lokal prüfen:

```bash
# PHP-Syntax aller eigenen Dateien
git ls-files '*.php' | xargs -n1 php -l

# JS-Syntax
node --check site/wp-content/themes/rechtsfux/assets/js/main.js

# Smoke-Test (Server muss laufen)
./tests/smoke.sh
```

Zwei GitHub-Actions müssen grün sein, bevor gemergt wird:
- **PHP Lint** — Syntax aller versionierten PHP-Dateien.
- **Smoke Test** — baut die Seite via `setup.sh` auf und prüft alle wichtigen Seiten.

## Commits

- Kleine, thematisch klar abgegrenzte Commits.
- Präfixe wie `feat:`, `fix:`, `docs:`, `test:`, `ci:`, `chore:` und ein Scope in Klammern,
  z. B. `feat(tools): …`.
- Titel im Imperativ, kurze Begründung im Body, wenn nötig.

## Code-Stil

- PHP: Tabs zur Einrückung, WordPress-nahe Namenskonvention (`rf_`-Präfix, snake_case).
- Ausgaben immer escapen (`esc_html`, `esc_url`, `esc_attr`).
- Siehe `.editorconfig` für Grundeinstellungen (Einrückung, Zeilenenden, Charset).
