# Adress-Autovervollständigung & PDF-Vorschau — Design

Datum: 2026-09-10
Status: Entwurf zur Freigabe

## Ziel

Zwei wiederverwendbare Bausteine für die Dokument-Formulare des Rechtsfux-Themes:

1. **Adress-Autovervollständigung:** Wer eine Adresse eingibt (Strasse, PLZ, Ort),
   soll ab wenigen Zeichen echte Vorschläge erhalten. Bei Auswahl füllen sich
   PLZ und Ort automatisch — man muss praktisch nur die Strasse tippen.
2. **PDF-Vorschau & Download:** Ein klar sichtbarer «Als PDF speichern»-Button
   überall, plus eine A4-getreue Vorschau des Dokuments, **bevor** man es
   herunterlädt. Niemand soll zum Download gezwungen sein, nur um zu sehen,
   wie das PDF aussieht.

## Entscheidungen (mit dem Nutzer geklärt)

- **Adressquelle: geo.admin.ch** (offizielle Schweizer Adresssuche des Bundes,
  `api3.geo.admin.ch`). Kostenlos, kein API-Key, kein Abrechnungskonto,
  CORS-fähig. Gewählt gegenüber den populären Google-Places-Plugins, weil diese
  ein Google-Cloud-Konto mit Kreditkarte verlangen, die Adressen an Google
  senden und für Schweizer Adressen keine bessere Qualität liefern. Bewusster
  Kompromiss: Für die Adresssuche verlässt der getippte Text neu den Browser
  (Bundes-API) — siehe «Datenschutz-Transparenz».
- **PDF: A4-Vorschau-Overlay + Browser-Druck.** Kein PDF-Framework. Ein Overlay
  zeigt das Dokument als echte A4-Seite (WYSIWYG); der Speichern-Knopf nutzt die
  Browser-Druckfunktion mit `@page`-A4-Regeln. Ergebnis ist vektor-scharf und
  der Text bleibt auswählbar.

## Ist-Zustand (relevant)

- `inc/doc-forms.php`: sieben Dokument-Generatoren (`rf_doc_*`). Jeder rendert
  ein `<form class="rf-form">` und eine Live-Vorschau `[data-preview]`. Die
  Vorschau-Bindung läuft über `<mark data-bind="feldname">`, gefüllt von
  `document-preview.js`.
- **Strukturierte Adressfelder** (getrennt Strasse/PLZ/Ort) gibt es bei:
  - `kuendigung-wohnung`: `abs_strasse/abs_plz/abs_ort`, `emp_strasse/emp_plz/emp_ort`
  - `kuendigung-arbeit`: `abs_strasse/abs_plz/abs_ort`, `ag_strasse/ag_plz/ag_ort`
  - `maengelruege`: `abs_strasse/abs_plz/abs_ort`, `emp_strasse/emp_plz/emp_ort`
  - Konvention: `<präfix>_strasse` / `<präfix>_plz` / `<präfix>_ort`.
- **Standalone-Ortsfelder:** `brief_ort` (fast überall), `ort` (Arbeitsvertrag),
  `wohnort` (Patientenverfügung, kombiniertes «8001 Zürich»).
- Die Vorschau-Leiste (`.rf-preview-bar` mit «Drucken» + «Kopieren») ist in allen
  sieben Generatoren **dupliziert**.
- `functions.php`: lädt `document-preview.js` nur auf `templates/document.php`.
- `@media print` in `main.css` blendet alles ausser `.rf-preview` aus.

## Komponente A — Adress-Autovervollständigung

### Neue Datei: `assets/js/address-autocomplete.js`

Eigenständiges Modul, auf Dokumentseiten geladen. Kapselt die geo.admin.ch-
Abfrage und die Dropdown-Interaktion. Kein Framework, Vanilla-JS im Stil der
bestehenden Skripte.

**Zielfelder (per Attribut, in `doc-forms.php` gesetzt):**
- `data-rf-street` auf jedem `<präfix>_strasse`-Input → Strassen-Autocomplete.
  Die Geschwisterfelder findet das Modul über den Namenspräfix
  (`abs_strasse` → `abs_plz`, `abs_ort`) im selben `<form>`.
- `data-rf-locality` auf `brief_ort`, `ort`, `wohnort` → Orts-/PLZ-Vorschläge
  (kein Strassenteil).

**Ablauf (Strasse):**
1. Eingabe ab **3 Zeichen**, **~200 ms** entprellt.
2. `fetch` mit `AbortController` (laufende Abfrage abbrechen) an:
   `https://api3.geo.admin.ch/rest/services/api/SearchServer?type=locations&origins=address&limit=8&searchText=<q>`
3. Ergebnisse aus `results[].attrs.label` parsen (siehe Parser). Dropdown
   anzeigen (Markup + Stil wie die Hero-Suche: `<a>`-Einträge, Hover/aktiv).
4. **Auswahl:** Strasse = «Strasse Nr.», PLZ- und Ort-Feld setzen, danach an jedem
   veränderten Feld ein `input`-Event auslösen, damit `document-preview.js` die
   Live-Vorschau aktualisiert.

**Ablauf (Locality):** wie oben, aber `origins=zipcode,gg25`; Auswahl schreibt
«PLZ Ort» (bzw. nur Ort, je nach Feld) in das eine Feld.

**Parser (`labelZuAdresse`) — reine Funktion, testbar:**
- Eingabe z. B. `"Bahnhofstrasse 1 <b>8001 Zürich</b>"`.
- `strasse` = Text vor `<b>`, HTML entfernt, getrimmt → `"Bahnhofstrasse 1"`.
- Inhalt zwischen `<b>…</b>` → `"8001 Zürich"`; `plz` = führende 4 Ziffern
  (`/^\d{4}/`), `ort` = Rest → `"Zürich"`.
- Zweisprachig robust: `"Zürichstrasse / Rue de Zurich 1 <b>2504 Biel/Bienne</b>"`
  → strasse `"Zürichstrasse / Rue de Zurich 1"`, plz `2504`, ort `"Biel/Bienne"`.
- Fällt der `<b>`-Block weg, gibt der Parser nur `strasse` zurück (Rest leer).

**Tastatur & Schliessen:** ↑/↓ Navigation, Enter übernimmt, Esc schliesst,
Aussenklick schliesst. Wiederverwendung der Interaktionsmuster der Hero-Suche.

**Graceful degradation:** Netzfehler, leere Antwort oder < 3 Zeichen → kein
Dropdown, Feld bleibt ein gewöhnliches Eingabefeld. Nichts blockiert die Eingabe.

### `functions.php`
- `address-autocomplete.js` auf `templates/document.php` einreihen (wie `rf-doc`).
- `preconnect` zu `https://api3.geo.admin.ch` für schnellere erste Abfrage.

### Datenschutz-Transparenz
- Dezenter Hinweis an den Adressblöcken: «Adresssuche via geo.admin.ch (Bund)».
- Startseiten-Werbung «Daten bleiben im Browser» bleibt vorerst unverändert;
  optionale Präzisierung wird dem Nutzer angeboten, nicht eigenmächtig geändert.

## Komponente B — PDF-Vorschau & «Als PDF speichern»

### `inc/doc-forms.php`
- Neuer Helfer **`rf_preview_bar()`**, der die duplizierte `.rf-preview-bar`
  ersetzt (sieben Stellen). Neue Aktionen: **«Als PDF speichern»** (primär) und
  «Kopieren». «Drucken» wandert in das Overlay.

### `assets/js/document-preview.js` (erweitert)
- Klick «Als PDF speichern» → Overlay `.rf-pdf-modal` öffnen. Es enthält eine
  **A4-Seite** `.rf-a4` mit einer Kopie des `.rf-preview`-Inhalts (identische
  Dokument-Stile → WYSIWYG). Das ist die Vorschau ohne Download.
- Overlay-Aktionen:
  - **«Als PDF speichern»**: `document.title` kurz auf den Dokumentnamen setzen
    (schöner Standard-Dateiname), `window.print()`, Titel danach zurücksetzen.
  - **«Drucken»**: dasselbe ohne Titel-Trick.
  - **«Schliessen»** / Esc / Klick auf den Backdrop schliesst.
- Body-Klasse `is-pdf-open`, solange das Overlay offen ist, damit die Druck-CSS
  gezielt die A4-Kopie druckt.

### `assets/css/main.css`
- `.rf-ac` (Autocomplete-Dropdown) — angelehnt an `.rf-command__results`.
- `.rf-pdf-modal` (Backdrop + zentriertes Panel, scrollbar), `.rf-a4`
  (A4-Proportion, weisser Grund, Schatten, Innenränder ~2 cm-Optik).
- Druckregeln: `@page { size: A4; margin: 2cm; }`. Bei `body.is-pdf-open` nur
  `.rf-a4 .rf-preview` drucken und Overlay-Chrome ausblenden; sonst greift die
  bestehende Regel (nur `.rf-preview`).
- Theme-fest (Overlay hell, da Dokument immer auf hellem Grund).

## Betroffene Dateien (Zusammenfassung)

- `inc/doc-forms.php` — `rf_preview_bar()`; 7 Leisten ersetzt; `data-rf-street`
  auf 3× `_strasse`-Inputs (6 Felder); `data-rf-locality` auf `brief_ort`/`ort`/
  `wohnort`; Adress-Hinweis.
- `assets/js/address-autocomplete.js` — **neu**.
- `assets/js/document-preview.js` — PDF-Overlay-Logik ergänzt.
- `assets/css/main.css` — Autocomplete-Dropdown, PDF-Overlay/A4, Druckregeln.
- `functions.php` — Skript einreihen, `preconnect`.

## Tests

- **Node-Unit-Test** von `labelZuAdresse` mit echten geo.admin.ch-Beispielen
  (inkl. zweisprachig, fehlender `<b>`-Block).
- **HTTP-Smoke** (`php -S`): Dokumentseite lädt, Skripte eingebunden,
  Autocomplete-Attribute und PDF-Button/Overlay-Markup vorhanden.
- **Interaktiv (Nutzer bestätigt visuell):** Tippen → Vorschläge; Auswahl füllt
  PLZ/Ort; PDF-Overlay zeigt A4 korrekt; «Als PDF speichern» erzeugt sauberes
  PDF. Kein Headless-Browser verfügbar (nur Safari), daher Nutzer-Sichtprüfung.

## Bewusst nicht enthalten (YAGNI)

- Keine Adress-Autocomplete auf freien Kombifeldern («Anna Muster, Zürich»).
- Kein PDF-Framework, kein serverseitiges PDF-Rendering.
- Keine Änderung der Startseiten-Marketingaussage (nur auf Wunsch).
- Keine Übernahme eines Drittanbieter-Plugins.
