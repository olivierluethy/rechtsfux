# Changelog

Alle nennenswerten Änderungen an diesem Projekt. Format angelehnt an
[Keep a Changelog](https://keepachangelog.com/de/1.1.0/).

## [1.1.0] — 2026-09-10

Funktionserweiterungen für die Dokument-Generatoren und Feinschliff an Suche & Layout.

### Dokument-Generatoren
- Echte PDF-Vorschau via **pdfmake + PDF.js** (Zoom, Suche) statt reiner HTML-Vorschau.
- **Adress-Autocomplete** über die geo.admin.ch-API mit A4-PDF-Vorschau.

### Suche
- **Levenshtein-Fuzzy-Suche** (Tippfehler-Toleranz) in der Live-Suche.
- Vorschlags-Dropdown wird nicht mehr abgeschnitten.

### Layout & Responsive
- Layout über die ganze Seite verdichtet und Responsive-Feinschliff.

### Behobene Probleme
- PDF-Signatur erzwang einen vorzeitigen Seitenumbruch — behoben.

[1.1.0]: https://github.com/olivierluethy/rechtsfux/releases/tag/v1.1.0

## [1.0.0] — 2026-09-09

Erste vollständige Fassung der Rechtsfux-Website (Demo).

### Website & Theme
- Individuelles WordPress-Theme „rechtsfux" (klassisch, ohne Build-Tools/Frameworks).
- WordPress mit **SQLite** (kein MySQL-Server nötig), lokal über den PHP-Dev-Server + Router.
- Startseite mit Hero-Befehlsleiste (Live-Suche), Prozess, Kategorien, Tools-Teaser,
  Rechtsschutz-Sektion, Ratgeber, FAQ und CTA.
- 6 Kategorien mit Mega-Menü (Desktop) / Akkordeon (Mobile); Kategorie-Übersichtsseiten.
- **Dark/Light-Mode** (System-Automatik + Toggle, kein Flash), voll responsive, 404-Seite.

### Dokument-Generatoren (Live-Vorschau, Drucken & Kopieren)
- Wohnung kündigen, Arbeitsstelle kündigen (inkl. **OR-Kündigungsfrist-Berechnung**),
  Arbeitsvertrag, Patientenverfügung, Auto-Kaufvertrag, Darlehensvertrag, Untermietvertrag,
  Mängelrüge.

### Rechner
- Erbschaft (CH-Erbrecht ab 2023), Bussen (Ordnungsbussen), MwSt (CH-Sätze),
  13.-Monatslohn (anteilig).

### Marke & SEO
- Geometrisches Fuchs-Logo, Vektor-Favicon, App-Icons, Web-Manifest, Theme-Screenshot.
- Open-Graph-/Twitter-Meta mit gerendertem Markenbild; robots.txt-Regeln; Core-Sitemap.

### Qualität & Werkzeuge
- `setup.sh` (reproduzierbare Erstinstallation), `start.sh`, `rf-seed.php` (idempotentes Seeding).
- Smoke-Test (`tests/smoke.sh`) über alle wichtigen Seiten.
- GitHub-Actions: **PHP Lint** und **Smoke Test**.
- MIT-Lizenz, README (Entwickler-Doku), CONTRIBUTING, `.editorconfig`.

### Behobene Probleme
- Dev-Router bediente Verzeichnisse wie `/wp-admin/` nicht (leitete auf die Startseite) — behoben.
- Horizontales Overflow auf Mobile (Header-CTA) — behoben.
- PHP-Warning beim Seeding (Multibyte-`»` in `"$topic"`) — behoben.

[1.0.0]: https://github.com/olivierluethy/rechtsfux
