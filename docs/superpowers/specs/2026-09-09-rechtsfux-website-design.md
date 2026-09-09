# Rechtsfux — WordPress-Website (Design-Spezifikation)

**Datum:** 2026-09-09
**Projekt:** Vollständige, individuell gestaltete WordPress-Website „Rechtsfux", inspiriert von vertragshilfe.ch, aber gestalterisch und funktional deutlich stärker.

---

## 1. Ziel & Erfolgskriterien

Eine Schweizer Rechtsdokument-Plattform („Rechtsfux") als **echte, lokale WordPress-Installation** mit individuellem Theme.

**Erfolg =**
- Wow-Effekt beim ersten Öffnen; wirkt präzise, professionell, hochwertig.
- **Vollständig responsive**, mobil hervorragend nutzbar (Kernkritik am Original: wirkt auf Laptop „zu groß" → straffere, kontrollierte Skala).
- **Dark/Light-Mode**: folgt automatisch der System-/Browser-Einstellung (`prefers-color-scheme`) **und** hat einen manuellen Umschalter (Auswahl wird gespeichert).
- Einfache, intuitive Navigation trotz tiefer Menüstruktur (Mega-Menü + saubere Mobile-Navigation).
- Inhalte im wp-admin bearbeitbar.
- Interaktive Elemente **funktionieren wirklich** (Rechner, Live-Dokumentvorschau).

**Rechtlich/ethisch:** Inspiriert von, aber **keine Imitation** der realen Firma (Kobo Rechtsschutz AG) — kein Fremd-Logo, kein fremder Markenname, keine als echt ausgegebenen fremden Firmendaten. Eigene Marke „Rechtsfux", Platzhalter-Impressumsdaten klar als solche.

---

## 2. Marke & Design-Sprache

**Name:** Rechtsfux — clever, nahbar, einprägsam („schlau wie ein Fuchs").
**Logo:** Stilisierter Fuchs-Kopf (geometrisch, aus Dreiecken) links neben der Wortmarke „Rechtsfux"; inline-SVG, damit er in Dark/Light mitfärbt.

**Farbwelt „Vertrauen & Frisch"** (als CSS-Custom-Properties / Theme-Tokens):

| Rolle | Light | Dark |
|-------|-------|------|
| Grund (bg) | `#F7F8F6` warmes Off-White | `#0E1512` tiefes Ink |
| Fläche/Karte | `#FFFFFF` | `#16201C` |
| Text primär | `#12201B` | `#EAF1EC` |
| Text sekundär | `#5A6B63` | `#9DB0A6` |
| Akzent (Emerald) | `#0E7C66` | `#2FD6A8` |
| Akzent-Hover | `#0B6353` | `#59E6BE` |
| Rahmen/Linie | `#E3E7E2` | `#26332D` |
| Signal/Warnung | `#C2410C` | `#F2A97A` |

Jede Farbe wird auf `:root` (Light) definiert und im Dark-Block überschrieben — nie nur in einem Media-Query. Dark-Regeln greifen sowohl über `@media (prefers-color-scheme: dark)` (nur wenn kein manuelles Light gesetzt) als auch über `[data-theme="dark"]`.

**Typografie:**
- Headlines: eine kräftige Grotesk (variabel), z.B. „Inter" bzw. „Space Grotesk" via Google Fonts, mit robustem System-Fallback-Stack.
- Fließtext: „Inter"/System-Sans, 16px Basis, ruhige Zeilenhöhe (~1.6).
- **Kontrollierte Typo-Skala** (Kernpunkt): Hero-Headline mobil ~2rem, Desktop max ~3.25rem (nicht die überdimensionierten Größen des Originals). Fluid via `clamp()`.

**Layout-Prinzipien:** 12-Spalten-Grid, Content-Max-Breite ~1200px, konsequenter 8px-Spacing-Rhythmus, großzügiger aber nicht übertriebener Weißraum, weiche Schatten, `border-radius` ~14px, subtile Hover-/Scroll-Micro-Interaktionen (reduziert bei `prefers-reduced-motion`).

---

## 3. Technische Architektur

**Stack:** Echtes WordPress (Core), lokal ausgeliefert per PHP-Built-in-Server. Datenbank **SQLite** über das offizielle Plugin `sqlite-database-integration` (kein MySQL-Server nötig → robust & sofort lauffähig). Vorhanden: PHP (Homebrew). Kein Docker, kein wp-cli nötig.

**Verzeichnis:** Projektwurzel `/Users/olivierluethy/Documents/firstWordpress`.
- `wordpress/` — WP-Core (heruntergeladen)
- `wordpress/wp-content/themes/rechtsfux/` — individuelles Theme (der eigentliche Kern der Arbeit)
- `wordpress/wp-content/db.php` + `sqlite-database-integration` Plugin — SQLite-Anbindung
- `wp-config.php` — konfiguriert für SQLite, `WP_HOME`/`WP_SITEURL` auf lokalen Host+Port
- Scripts im Projektwurzel: `start.sh` (Server starten), `setup.php`/`seed`-Mechanismus (Seiten, Menüs, Optionen automatisiert anlegen).

**Theme-Aufbau (klassisches Theme mit sauberer Teilung):**
```
themes/rechtsfux/
  style.css              (Theme-Header + Import der echten Styles)
  functions.php          (Setup, Menüs, Enqueue, Theme-Support, Helper)
  assets/
    css/main.css         (Design-Tokens, Layout, Komponenten, Dark/Light)
    js/main.js           (Theme-Toggle, Mega-Menü, Mobile-Nav, Scroll-FX)
    js/calculators.js     (Rechner-Logik)
    js/document-preview.js (Live-Dokumentvorschau)
  inc/
    seed.php             (Programmatisches Anlegen von Seiten/Menüs/Optionen)
  parts/                 (header, footer, mega-menu, hero, card, faq …)
  templates/             (Seiten-Templates: category, tool, document, article)
  header.php / footer.php / front-page.php / index.php / page.php / 404.php
```

**Isolation:** Jede Komponente (Header, Mega-Menü, Hero, Karten-Grid, Rechner, Dokumentvorschau, FAQ, Footer) ist ein eigenständiger, wiederverwendbarer Template-Part bzw. JS-Modul mit klar definierter Schnittstelle. JS-Module sind unabhängig, ohne geteilten Zustand außer dem Theme-Token (`data-theme` auf `<html>` + `localStorage`).

**Dark/Light-Mechanik:** Inline-Skript im `<head>` setzt `data-theme` vor dem Paint (kein Flash), liest `localStorage` → sonst `prefers-color-scheme`. Umschalter aktualisiert beide.

---

## 4. Informationsarchitektur / Navigation

Hauptmenü mit Mega-Menü (Desktop) bzw. Akkordeon (Mobile):

1. **Kündigungsschreiben** (Mega-Menü mit Beispiel-Einträgen: Wohnung, Arbeitsstelle, Parkplatz, Fitness, Versicherung, Telecom, Abo, Verein …)
   - Übersichtsseite (voll)
   - **Detailseite „Wohnung kündigen"** (voll, mit Live-Dokumentvorschau)
2. **Verträge** (Auto-Kauf, Arbeitsvertrag, Darlehen, Untermiete, Schenkung …)
   - Übersichtsseite (voll)
   - **Detailseite „Arbeitsvertrag"** (voll, mit Live-Vorschau)
3. **Vorsorgedokumente** (Patientenverfügung, Vorsorgeauftrag, Betreuungsanweisung …)
   - Übersichtsseite (voll)
   - **Detailseite „Patientenverfügung"** (voll, mit Live-Vorschau)
4. **Musterbriefe** (Mängelrüge, Einsprache, Beschwerde …)
   - Übersichtsseite (voll)
5. **Tools**
   - Übersichtsseite + **3 funktionierende Rechner**: Erbschaftsrechner, Bussenrechner, MwSt-Rechner
6. **Ratgeber** (Blog)
   - Übersichtsseite + **1 ausgearbeiteter Artikel**
- Sekundär/Footer: Über uns, Kontakt, Impressum, Datenschutz, AGB (Platzhalterinhalt, klar gekennzeichnet).

Nicht-ausgearbeitete Unterpunkte existieren als Menülinks/Karten und führen auf die jeweilige Kategorie-Übersicht (keine toten Links), sind aber nicht einzeln als Vollseite gebaut (YAGNI, gemäß gewähltem Umfang „Struktur + Beispiel-Tiefe").

---

## 5. Startseite — Sektionen

1. **Hero:** Value-Proposition-Headline, Subline, prominentes Such-/Auswahlfeld („Was möchtest du erledigen?"), primärer CTA. Rechts/hintergründig ein feines geometrisches Motiv (Fuchs/Grid), kein schweres Bild.
2. **Vertrauens-Leiste:** kompakte Kennzahlen/Badges (z.B. „In 2 Minuten fertig", „Schweizer Recht", „Datenschutz — automatische Löschung").
3. **So funktioniert's:** 3-Schritt-Prozess (Auswählen → Ausfüllen → Herunterladen).
4. **Kategorien-Grid:** 6 Karten zu den Hauptbereichen mit Icon, Kurztext, Hover-Effekt.
5. **Tools-Teaser:** die 3 Rechner anteasern, Link in den Tools-Bereich.
6. **Rechtsschutz-Partner-Sektion:** Vertrauen/„im Ernstfall abgesichert" (generisch, kein realer Partnername).
7. **Ratgeber-Teaser:** 3 Artikelkarten.
8. **FAQ:** Akkordeon, 5–6 Fragen.
9. **Abschluss-CTA + Footer:** großer CTA-Block, danach strukturierter Footer (Spalten, Rechts-Links, Theme-Toggle auch hier, Sprach-/Copyright-Zeile).

---

## 6. Interaktive Features (funktionsfähig)

**A) Rechner (Vanilla JS, echte Berechnung, keine Server-Runde nötig):**
- **Bussenrechner:** Auswahl Vergehen (z.B. Tempoüberschreitung innerorts/ausserorts/Autobahn) + Wert → geschätzte Busse anhand hinterlegter Staffelung (klar als Orientierung/ohne Gewähr gekennzeichnet).
- **MwSt-Rechner:** Betrag + Satz (Normal/Reduziert/Sonder) + Richtung (brutto↔netto) → Ergebnis, Aufschlüsselung.
- **Erbschaftsrechner:** Nachlasswert + Konstellation (Ehepartner/Kinder/Eltern) → gesetzliche Erbteile & Pflichtteile nach CH-ZGB (vereinfacht, gekennzeichnet).

**B) Live-Dokumentvorschau (auf den 3 Detailseiten):**
- Formularfelder (Name, Adresse, Vertragspartner, Datum, Kündigungsgrund/-termin …) füllen **in Echtzeit** eine formatierte Dokumentvorschau daneben.
- Buttons „Drucken" (window.print mit Print-CSS) und „Als Text kopieren". Kein Backend-Versand; Hinweis „Daten bleiben im Browser".

**C) UI-Interaktionen:** Theme-Toggle, Mega-Menü (Hover/Focus, tastaturzugänglich), Mobile-Off-canvas-Nav mit Akkordeon, sanfte Reveal-Animationen beim Scrollen (IntersectionObserver), reduziert bei `prefers-reduced-motion`.

---

## 7. Barrierefreiheit & Qualität

- Semantisches HTML, ausreichende Kontraste in beiden Themes, Fokus-Sichtbarkeit, Tastaturbedienbarkeit von Menü/Akkordeon/Toggle, `aria`-Attribute wo nötig.
- Responsive Breakpoints: ~360 / 640 / 900 / 1200. Kein horizontales Scrollen; breite Elemente (Tabellen/Vorschau) scrollen in eigenem Container.
- Performance: Fonts mit `display=swap` + Fallback, JS defer, keine schweren Libraries (Vanilla JS).

---

## 8. Umsetzungs-Schritte (Überblick)

1. WordPress-Core lokal beschaffen + SQLite-Plugin einrichten, `wp-config.php` konfigurieren.
2. Erstinstallation automatisiert durchführen (Admin-User, Seitentitel), Server via `start.sh`.
3. Individuelles Theme „rechtsfux" aufbauen: Tokens, Layout, Komponenten, Dark/Light, Header/Footer/Mega-Menü.
4. Seiten & Menüs programmatisch seeden (`inc/seed.php`), Templates zuweisen.
5. Startseite + 4 Kategorie-Übersichten + 3 Detailseiten + Tools + Ratgeber + Rechtliches bauen.
6. Interaktive Features implementieren (Rechner, Live-Vorschau).
7. Responsive-, Dark/Light- und Barrierefreiheits-Durchgang; Feinschliff.
8. Verifikation: Seite läuft lokal, alle Navigationslinks funktionieren, Rechner rechnen, Vorschau füllt, kein Flash beim Theme-Wechsel.

---

## 9. Bewusst NICHT im Umfang (YAGNI)

- Nicht alle 40+ Dokument-Detailseiten (nur 3 Beispiele voll ausgebaut).
- Kein echter Zahlungs-/Login-Flow, keine E-Mail-Versendung, kein CRM.
- Kein Multi-Language (nur Deutsch/CH).
- Kein Live-Hosting/Deployment in diesem Schritt (rein lokal; später überführbar).
