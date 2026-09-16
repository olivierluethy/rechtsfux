# Live-Vorschau: Kopierschutz & serverseitige Paywall-Absicherung (Vormerkung)

**Datum:** 2026-09-16
**Status:** Client-seitiger Schutz umgesetzt · serverseitige Absicherung vorgemerkt

## Kontext

Zukünftig sollen gewisse Vorlagen kostenpflichtig werden. Die Live-Vorschau
(`.rf-preview`) darf den vollständigen Vertragsinhalt dann nicht mehr gratis
herausgeben, sonst wäre die Paywall wertlos.

## Umgesetzt (Stand heute)

Schutz gegen **Gelegenheits-Kopieren** per Maus:

- `assets/css/main.css` — `.rf-preview` erhält `user-select: none`
  (inkl. `-webkit-`/`-moz-`-Prefixe) und `-webkit-touch-callout: none`.
  → Kein Markieren per Maus, kein iOS-Longpress-Kopieren.
- `assets/js/document-preview.js` — `contextmenu`-Event auf der Vorschau wird
  abgefangen (kein Rechtsklick → Kopieren).
- Copy-Button (`.rf-copy`) ist **paywall-vorbereitet**: `isLocked()` liest
  `data-locked` am `.rf-doc`. Ist das Attribut gesetzt, wird der Button
  entfernt. Heute ist nichts gesperrt → Verhalten unverändert; eine spätere
  Paywall muss nur `data-locked` setzen.
- Der PDF-Weg (`.rf-pdf`, „Als PDF speichern") bleibt bewusst unangetastet.

Im Browser (Chromium/Playwright) verifiziert: `user-select: none`,
programmatische Selektion der Vorschau liefert leeren Text, Formular bleibt
editierbar, Eingaben erscheinen live, Kontextmenü verhindert.

## Bewusst NICHT umgesetzt: Dev-Tools-Sperre

Dev Tools und „Seitenquelltext anzeigen" lassen sich clientseitig **nicht
zuverlässig** aussperren. Die Live-Vorschau wird komplett im Browser aus dem
PHP-Template + Nutzereingaben zusammengebaut, der volle Text steht also im DOM
und im HTML-Quelltext. „Anti-Dev-Tools"-Tricks (F12 blockieren,
`debugger`-Schleifen, Dev-Tools-Erkennung) sind trivial umgehbar, stören echte
Nutzer und erzeugen nur Scheinsicherheit. Deshalb bewusst weggelassen.

## Vorgemerkt: serverseitige Absicherung für die echte Paywall

Echter Schutz gegen Kopieren via Quelltext/Dev Tools geht **nur serverseitig** —
der bezahlpflichtige Inhalt darf gar nicht erst vollständig an den Browser
gesendet werden. Optionen für die spätere Umsetzung:

1. **Teaser-Vorschau:** Server liefert für gesperrte Vorlagen nur einen
   Ausschnitt (z. B. erster Absatz); Volltext erst nach Bezahlung. Der Rest ist
   nicht im HTML.
2. **Serverseitiges Bild + Wasserzeichen:** Vorschau als serverseitig
   gerendertes Bild mit Watermark statt echtem Text (Text nie im DOM).
3. **PDF serverseitig gaten:** Das Voll-Dokument wird erst nach Bezahlung
   erzeugt/freigegeben. Achtung: Aktuell baut auch das PDF clientseitig aus dem
   DOM (`pdfmake` liest `.rf-preview`) — für eine echte Paywall muss die
   PDF-Erzeugung für bezahlte Vorlagen serverseitig laufen.

Der bestehende `data-locked`-Schalter ist der vorgesehene Ankerpunkt, an dem die
Paywall-Logik später greift.
