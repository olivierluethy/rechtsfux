# 🦊 Rechtsfux

**Das clevere Schweizer Vertrags-Werkzeug** — eine vollständige, individuell gestaltete
WordPress-Website. Kündigungen, Verträge und Vorsorgedokumente entstehen in unter zwei
Minuten: ausfüllen, live sehen, drucken. Dazu drei funktionierende Rechner, Mega-Menü,
Dark/Light-Mode und ein durchgängig responsives, straffes „Schweizer-Präzisions"-Design.

> **Demo-Projekt.** „Rechtsfux" ist eine **fiktive Marke**. Alle Firmen-, Impressums- und
> Kontaktangaben sind Platzhalter. Die Vorlagen/Rechner dienen der Orientierung und ersetzen
> keine Rechtsberatung im Einzelfall.

---

## Inhalt

- [Inspiration & Abgrenzung](#inspiration--abgrenzung)
- [Schnellstart](#schnellstart)
- [Login / WordPress-Backend](#login--wordpress-backend)
- [Technologie-Stack](#technologie-stack)
- [Projektstruktur](#projektstruktur)
- [Theme-Architektur](#theme-architektur)
- [Design-Entscheidungen & Begründung](#design-entscheidungen--begründung)
- [Inhalte & wie man erweitert](#inhalte--wie-man-erweitert)
- [Umgesetzt / bewusst NICHT umgesetzt](#umgesetzt--bewusst-nicht-umgesetzt)
- [Gelöste Probleme (für die Nachwelt)](#gelöste-probleme-für-die-nachwelt)
- [Barrierefreiheit & Qualität](#barrierefreiheit--qualität)
- [Von Demo zu Produktion](#von-demo-zu-produktion)
- [Befehls-Spickzettel](#befehls-spickzettel)

---

## Inspiration & Abgrenzung

Die Seite ist **inspiriert von [vertragshilfe.ch](https://www.vertragshilfe.ch)** (Betreiberin: Kobo
Rechtsschutz AG) — dem Schweizer „Taschenmesser für Verträge" mit Bereichen wie Kündigungsschreiben,
Verträge, Vorsorgedokumente, Musterbriefe, Tools und Ratgeber.

Rechtsfux baut dieselbe Grundidee nach, macht sie aber **gezielt besser**:

- **Straffer statt aufgebläht.** Kernkritik am Original: wirkt auf dem Laptop zu gross. Antwort:
  kontrollierte Typo-Skala (`clamp()`), engeres Grid, mehr Ruhe.
- **Mobile-first gedacht**, nicht nachträglich verkleinert.
- **Echte Interaktion** statt Teaser: Rechner rechnen wirklich, Dokumente füllen eine Live-Vorschau.
- **Dark/Light-Mode** inkl. System-Automatik.

**Wichtig:** Es ist **keine Imitation** der realen Firma — eigener Name, eigenes Logo, eigene Texte,
Platzhalter-Impressum. Kein fremdes Branding, keine als echt ausgegebenen fremden Firmendaten.

## Schnellstart

Voraussetzungen: **PHP 8+ mit `pdo_sqlite`**, `curl`, `tar`, `unzip` (auf macOS via Homebrew vorhanden).

```bash
./setup.sh     # holt WordPress-Core + SQLite-Plugin, installiert & seedet alles
./start.sh     # startet den lokalen Server
```

Dann im Browser: **http://localhost:8080**

- Dark-Mode direkt ansehen: an jede URL `?rf-theme=dark` (bzw. `?rf-theme=light`) hängen.
- Server manuell: `cd site && php -S localhost:8080 -t . router.php`

## Login / WordPress-Backend

> **Am zuverlässigsten über die Login-Datei direkt:**
> **http://localhost:8080/wp-login.php**

| | |
|---|---|
| **Benutzername** | `admin` |
| **Passwort** | `Rechtsfux2026!` |
| Backend | http://localhost:8080/wp-admin |

Nach dem ersten Login das Passwort unter **Benutzer → Profil** ändern.

> **Warum `wp-login.php` und nicht `/wp-admin`?** In einer frühen Version leitete der Dev-Router
> `/wp-admin` fälschlich auf die Startseite. WordPress antwortete dabei mit einem **301 (permanent)**,
> das Browser **dauerhaft cachen**. Der Router ist längst gefixt (siehe unten), aber ein Browser mit
> altem Cache springt evtl. weiter zur Home. `wp-login.php` war davon nie betroffen — deshalb der
> direkte Weg. Alternativ: Inkognito-Fenster oder Cache für `localhost` leeren.

Im Backend bearbeitbar: Texte von **Impressum/Datenschutz/AGB**, die **Ratgeber-Beiträge**, Menüs und
Titel. Das eigentliche **Design steckt bewusst im Theme-Code** (nicht im Seiteneditor) — so bleibt
alles konsistent und responsiv.

## Technologie-Stack

- **WordPress** (Core) als CMS — ausgeliefert über den **PHP-Built-in-Server** (`php -S`) mit
  eigenem `router.php`.
- **SQLite** als Datenbank über das offizielle Plugin *SQLite Database Integration* (kein MySQL-Server
  nötig). Datei: `site/wp-content/database/rechtsfux.sqlite`, angebunden per `wp-content/db.php`.
- **Individuelles Theme** (`rechtsfux`) — klassisches Theme, kein Page-Builder, **keine Build-Tools**,
  **keine JS-Frameworks**. Reines PHP + Vanilla-JS + eine handgeschriebene CSS-Datei.
- **Google Fonts:** *Bricolage Grotesque* (Display) + *Hanken Grotesk* (Text).

## Projektstruktur

```
firstWordpress/
├─ README.md                 ← dieses Dokument
├─ setup.sh                  ← Core + SQLite beschaffen, installieren, seeden (idempotent)
├─ start.sh                  ← lokalen Server starten
├─ docs/superpowers/specs/   ← Design-Spezifikation (Brainstorming-Ergebnis)
└─ site/                     ← WordPress-Root (Core via .gitignore ausgeschlossen)
   ├─ wp-config.php          ← SQLite-Konfiguration, lokale URLs   [versioniert]
   ├─ router.php             ← Router für den PHP-Dev-Server        [versioniert]
   ├─ rf-seed.php            ← Seed-Runner (Inhalte neu aufbauen)   [versioniert]
   └─ wp-content/themes/rechtsfux/          ← DAS THEME              [versioniert]
      ├─ style.css                 Theme-Header
      ├─ functions.php             Setup, Assets, Helfer (rf_url etc.)
      ├─ header.php / footer.php    Header (Mega-Menü, No-Flash-Theme) / Footer
      ├─ index.php / page.php / single.php / 404.php   WP-Kern-Templates
      ├─ inc/
      │   ├─ ia.php                Informationsarchitektur (zentrale Datenquelle)
      │   ├─ icons.php             Inline-SVG-Icons + Fuchs-Logo
      │   ├─ doc-forms.php         3 Dokument-Formulare + Live-Vorschauen
      │   └─ seed.php              Seiten/Menüs/Blog/Optionen anlegen
      ├─ templates/
      │   ├─ front.php             Startseite
      │   ├─ category.php          Kategorie-Übersicht
      │   ├─ document.php          Dokument-Generator
      │   ├─ tools.php             Rechner-Seite
      │   ├─ ratgeber.php          Blog-Übersicht
      │   ├─ about.php / contact.php
      └─ assets/
          ├─ css/main.css          Design-System (Tokens, Komponenten, Dark/Light, Responsive)
          └─ js/  main.js          UI (Theme-Toggle, Menüs, FAQ, Reveal, Hero-Suche)
                  calculators.js   Erbschafts-/Bussen-/MwSt-Rechner
                  document-preview.js  Live-Vorschau (Drucken/Kopieren)
```

**WordPress-Core wird nicht versioniert** (siehe `.gitignore`) — `setup.sh` beschafft ihn, der Seeder
erzeugt die DB. So bleibt das Repo schlank und jeder Commit dreht sich um eigene Arbeit.

## Theme-Architektur

**Ein Grundprinzip: `inc/ia.php` ist die einzige Quelle der Wahrheit.** Diese Datei beschreibt die
komplette Informationsarchitektur (6 Kategorien mit Slug, Tagline, Icon, Items, Detailseiten). Aus ihr
speisen sich **gleichermassen**:

1. das **Mega-Menü** und die **Mobile-Navigation** (`header.php`),
2. der **Suchindex** der Hero-Befehlsleiste (`templates/front.php`),
3. der **Seeder**, der die passenden WordPress-Seiten und das Menü anlegt (`inc/seed.php`),
4. die **Kategorie- und Footer-Links**.

Wer die Struktur ändern will, ändert nur `ia.php` und lässt neu seeden — alles Übrige zieht nach.

**Rendering-Fluss:** WordPress-Seiten sind reale Einträge (per Seeder angelegt), tragen aber ein
**Page-Template** (`_wp_page_template`), das das Layout liefert. Die Inhalte der Design-Seiten stecken
also im Template-Code, nicht im Editor — bewusst, für Konsistenz und Responsivität. `rf_url($slug)`
löst Slugs (auch verschachtelte Kinder und `#anker`) robust in Permalinks auf.

**Dark/Light ohne Flash:** Ein Inline-Skript im `<head>` setzt `data-theme` **vor dem ersten Paint**
(aus `?rf-theme=`, sonst `localStorage`, sonst greift `prefers-color-scheme`). Der Toggle in
`main.js` schreibt die Wahl zurück. Alle Farben sind Tokens; Dark wird **sowohl** über
`@media (prefers-color-scheme: dark)` als auch über `[data-theme="dark"]` definiert — nie nur in einem.

**Dokument-Generator (Live-Vorschau):** In `inc/doc-forms.php` rendert jede Vorlage ein Formular und
daneben eine Vorschau. Die Bindung ist bewusst simpel und generisch: jedes `<mark data-bind="feld">`
in der Vorschau wird von `document-preview.js` mit dem Wert von `[name="feld"]` gefüllt (sonst
Platzhalter). Datumsfelder (`data-date`) werden auf Deutsch formatiert. Nichts verlässt den Browser.

**Rechner:** `calculators.js` enthält drei unabhängige Module (Erbschaft, Bussen, MwSt), die live und
ohne Server-Runde rechnen und im `de-CH`-Format ausgeben.

## Design-Entscheidungen & Begründung

**Konzept: „Schweizer Präzision trifft cleveren Fuchs."** Zielgruppe: Menschen in der Schweiz, die
Alltags-Rechtsthemen selbst und korrekt erledigen wollen. Haltung: kompetent, ruhig, nahbar.

- **Marke „Rechtsfux"** — clever, einprägsam („schlau wie ein Fuchs"). Logo: geometrischer Fuchs-Kopf
  aus Dreiecken, als Inline-SVG (färbt in Dark/Light mit).
- **Typografie:** *Bricolage Grotesque* (Display, charaktervoll-präzise) + *Hanken Grotesk* (Text) —
  bewusst **nicht** das übliche Inter/Space-Grotesk-Duo, um nicht generisch zu wirken.
- **Held der Startseite:** eine echte **Befehlsleiste** („Was möchten Sie erledigen?") statt der
  generischen Riesenzahl — das Werkzeug *ist* das Produkt.
- **Motiv:** feine Raster-/„Massstab"-Linien als Referenz an Recht & Präzision (dezent, nie Deko-Lärm).

**Farbwelt „Vertrauen & Frisch"** (als CSS-Tokens in `main.css`):

| Rolle | Light | Dark |
|---|---|---|
| Grund | `#F4F6F3` warmes Off-White | `#0C1310` tiefes Ink |
| Fläche | `#FFFFFF` | `#14201B` |
| Text | `#10201A` | `#E9F1EC` |
| Akzent (Emerald) | `#0E7C66` | `#2FD6A8` |
| Signal (Fuchs-Bernstein) | `#C4691B` | `#E9A66A` |

Emerald ist die **Handlungs-/Vertrauensfarbe**, der warme Bernstein ein **sparsames Signal**
(Hero-Unterstrich, Hinweise) — der Kühl/Warm-Kontrast gibt Persönlichkeit.

**Bewusst gegen „AI-Optik":** kein Creme+Serif+Terrakotta, keine ALL-CAPS-Eyebrows, keine
`01/02/03`-Marker (ausser beim echten 3-Schritt-Prozess), keine identischen Karten mit demselben
Schatten überall, keine `→`-Suffixe an jedem Link. Bold wird an **einer** Stelle ausgegeben (Hero),
der Rest bleibt ruhig.

## Inhalte & wie man erweitert

**Neue Detailseite / Dokument-Generator hinzufügen:**
1. In `inc/ia.php` beim Item `'ready' => true` setzen (oder Item ergänzen).
2. In `rf_documents()` (`ia.php`) einen Eintrag mit `title`, `parent`, `doc`, `lead` anlegen.
3. In `inc/doc-forms.php` eine `rf_doc_<typ>()`-Funktion schreiben (Formular + `<mark data-bind>`-Vorschau)
   und im `switch` von `rf_render_document()` verdrahten.
4. `cd site && php rf-seed.php` → Seite entsteht als Kind ihrer Kategorie.

**Neuen Menü-/Kategorie-Eintrag:** nur in `inc/ia.php` ergänzen, dann neu seeden.

**Neuen Rechner:** Markup-Karte in `templates/tools.php` (mit `id`/Ankern), Logik-Modul in
`calculators.js` (Muster wie die bestehenden drei).

**Inhalte im Backend:** Ratgeber-Beiträge und Rechtstexte lassen sich normal in `wp-admin` pflegen.

## Umgesetzt / bewusst NICHT umgesetzt

**Umgesetzt**
- Startseite (Hero-Befehlsleiste mit Live-Suche, Prozess, Kategorien, Tools-Teaser, Rechtsschutz,
  Ratgeber, FAQ, CTA).
- 6 Kategorien mit Mega-Menü / Mobile-Akkordeon; Kategorie-Übersichtsseiten.
- **3 voll funktionsfähige Dokument-Generatoren** mit Live-Vorschau, Drucken & Kopieren:
  Wohnung kündigen · Arbeitsvertrag · Patientenverfügung.
- **3 funktionierende Rechner:** Erbschaft (CH-Erbrecht ab 2023), Bussen (Ordnungsbussen), MwSt.
- Ratgeber-Blog (1 ausgearbeiteter Artikel + 2 Beispiele), Über uns, Kontakt (Demo-Formular),
  Impressum/Datenschutz/AGB (Platzhalter).
- Dark/Light-Mode (System-Automatik + Toggle, kein Flash), voll responsive, 404-Seite.

**Bewusst NICHT umgesetzt (YAGNI / Scope „Struktur + Beispiel-Tiefe")**
- Nicht alle 40+ Dokumentseiten des Vorbilds — nur 3 exemplarisch voll ausgebaut; die übrigen sind
  als Navigation/Karten mit „bald"-Kennzeichnung angelegt und führen nie ins Leere.
- Kein Zahlungs-/Login-Flow für Endkunden, kein E-Mail-Versand, kein CRM.
- Kein Mehrsprachen-Support (nur Deutsch/CH).
- Kein Live-Hosting/Deployment (rein lokal; siehe „Von Demo zu Produktion").

## Gelöste Probleme (für die Nachwelt)

Diese Punkte sind in der Git-Historie als eigene `fix:`-Commits dokumentiert:

1. **`/wp-admin` → Startseite** *(Router)*. Der Dev-Router reichte Verzeichnis-URLs an
   `index.php` weiter, statt deren eigenes `index.php` auszuführen. Fix: Verzeichnisse erkennen und ihr
   `index.php` mit gesetztem `SCRIPT_NAME`/`SCRIPT_FILENAME` ausführen. **Achtung Browser-Cache:** die
   alte 301-Weiterleitung bleibt evtl. gecacht → über `wp-login.php` einloggen.
2. **Horizontales Overflow auf Mobile** *(CSS)*. Der nicht schrumpfende Header-CTA schob das Layout
   über die Viewportbreite. Fix: `overflow-x: clip` auf `html`, Header-CTA unter 860 px ausblenden.
   (Der „abgeschnittene" Eindruck in Headless-Screenshots war zusätzlich ein Artefakt der 500-px-
   Mindestbreite von headless Chrome — kein echter Bug.)
3. **PHP-Warning beim Seeding** *(Seeder)*. `«$topic»` in doppelten Anführungszeichen: PHP zählt das
   Multibyte-Guillemet `»` zum Bezeichner. Fix: `{$topic}` klammern.
4. **Theme gezielt testbar** *(Header)*. `?rf-theme=dark|light` erzwingt ein Schema (ohne zu
   persistieren) — praktisch für Tests/Vorführung.

## Barrierefreiheit & Qualität

Semantisches HTML, ausreichende Kontraste in beiden Themes, sichtbarer Fokus, Tastaturbedienung von
Menü/Akkordeon/Toggle, `aria`-Attribute; `prefers-reduced-motion` wird respektiert; Fonts mit
`display=swap` + System-Fallback; JS `defer`, keine schweren Libraries; Print-CSS für die
Dokument-Vorschau; kein horizontales Scrollen.

## Von Demo zu Produktion

- **Salts neu erzeugen** in `wp-config.php` (aktuell lokale Demo-Werte).
- **Admin-Passwort ändern** (Standard `Rechtsfux2026!` ist nur fürs lokale Setup).
- Für echten Betrieb ggf. auf **MySQL/MariaDB** wechseln (DB-Konstanten in `wp-config.php`, `db.php`
  entfernen) oder die SQLite-Datei mit ausliefern.
- Hinter einem echten Webserver (Apache/nginx) wird `router.php` nicht gebraucht — dort greifen die
  normalen WordPress-Rewrite-Regeln.
- Platzhalter in Impressum/Datenschutz/AGB durch echte Angaben ersetzen.

## Befehls-Spickzettel

```bash
./setup.sh                                  # Erstinstallation (Core, SQLite, Install, Seed)
./start.sh                                  # Server starten (http://localhost:8080)
cd site && php rf-seed.php                   # Seiten/Menüs/Blog neu aufbauen (idempotent)
cd site && php -S localhost:8080 -t . router.php   # Server manuell
```

Login: **http://localhost:8080/wp-login.php** · `admin` / `Rechtsfux2026!`
