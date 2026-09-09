<?php
/**
 * Informationsarchitektur — zentrale Datenquelle für Navigation, Hub-Seiten und Seeding.
 *
 * @package Rechtsfux
 */

defined( 'ABSPATH' ) || exit;

/**
 * Liefert die komplette Navigations-/Inhaltsstruktur.
 *
 * Jede Kategorie: slug, label, tagline, icon, template, featured (voll gebaute Detailseite),
 * items (Liste aus label/slug/desc/ready). ready=false verlinkt auf die Hub-Seite.
 */
function rf_ia() {
	static $ia = null;
	if ( null !== $ia ) {
		return $ia;
	}

	$ia = array(
		array(
			'slug'     => 'kuendigungsschreiben',
			'label'    => 'Kündigungsschreiben',
			'nav'      => 'Kündigen',
			'tagline'  => 'Verträge sauber und fristgerecht beenden — in unter zwei Minuten.',
			'icon'     => 'termination',
			'template' => 'category',
			'featured' => 'wohnung-kuendigen',
			'items'    => array(
				array( 'label' => 'Wohnung kündigen', 'slug' => 'wohnung-kuendigen', 'desc' => 'Mietvertrag fristgerecht beenden, mit Termin-Rechner.', 'ready' => true ),
				array( 'label' => 'Arbeitsstelle kündigen', 'slug' => 'arbeitsstelle-kuendigen', 'desc' => 'Ordentliche Kündigung mit korrekter Frist.', 'ready' => true ),
				array( 'label' => 'Parkplatz kündigen', 'slug' => 'parkplatz-kuendigen', 'desc' => 'Stellplatz oder Garage abmelden.', 'ready' => false ),
				array( 'label' => 'Fitness-Abo kündigen', 'slug' => 'fitness-kuendigen', 'desc' => 'Mitgliedschaft ohne Verlängerung beenden.', 'ready' => false ),
				array( 'label' => 'Versicherung kündigen', 'slug' => 'versicherung-kuendigen', 'desc' => 'Police fristgerecht auflösen.', 'ready' => false ),
				array( 'label' => 'Handy & Internet kündigen', 'slug' => 'telecom-kuendigen', 'desc' => 'Telecom-Abo sauber beenden.', 'ready' => false ),
				array( 'label' => 'Streaming-Abo kündigen', 'slug' => 'streaming-kuendigen', 'desc' => 'Digitale Abos loswerden.', 'ready' => false ),
				array( 'label' => 'Vereinsmitgliedschaft kündigen', 'slug' => 'verein-kuendigen', 'desc' => 'Austritt korrekt erklären.', 'ready' => false ),
			),
		),
		array(
			'slug'     => 'vertraege',
			'label'    => 'Verträge',
			'nav'      => 'Verträge',
			'tagline'  => 'Rechtssichere Schweizer Vorlagen — mit Erklärung bei jedem Feld.',
			'icon'     => 'contract',
			'template' => 'category',
			'featured' => 'arbeitsvertrag',
			'items'    => array(
				array( 'label' => 'Arbeitsvertrag', 'slug' => 'arbeitsvertrag', 'desc' => 'Anstellung sauber regeln, inkl. Probezeit & Pensum.', 'ready' => true ),
				array( 'label' => 'Auto-Kaufvertrag', 'slug' => 'auto-kaufvertrag', 'desc' => 'Privatverkauf ab Platz absichern.', 'ready' => true ),
				array( 'label' => 'Darlehensvertrag', 'slug' => 'darlehensvertrag', 'desc' => 'Privates Darlehen mit Rückzahlung.', 'ready' => true ),
				array( 'label' => 'Untermietvertrag', 'slug' => 'untermietvertrag', 'desc' => 'Zimmer oder Wohnung untervermieten.', 'ready' => true ),
				array( 'label' => 'Mietvertrag Parkplatz', 'slug' => 'mietvertrag-parkplatz', 'desc' => 'Stellplatz rechtssicher vermieten.', 'ready' => false ),
				array( 'label' => 'Schenkungsvertrag', 'slug' => 'schenkungsvertrag', 'desc' => 'Schenkung klar dokumentieren.', 'ready' => false ),
			),
		),
		array(
			'slug'     => 'vorsorgedokumente',
			'label'    => 'Vorsorgedokumente',
			'nav'      => 'Vorsorge',
			'tagline'  => 'Vorausschauend entscheiden — für den Fall, dass Sie es nicht mehr können.',
			'icon'     => 'shield',
			'template' => 'category',
			'featured' => 'patientenverfuegung',
			'items'    => array(
				array( 'label' => 'Patientenverfügung', 'slug' => 'patientenverfuegung', 'desc' => 'Medizinische Wünsche verbindlich festhalten.', 'ready' => true ),
				array( 'label' => 'Vorsorgeauftrag', 'slug' => 'vorsorgeauftrag', 'desc' => 'Vertretung bei Urteilsunfähigkeit bestimmen.', 'ready' => false ),
				array( 'label' => 'Betreuungsanweisung', 'slug' => 'betreuungsanweisung', 'desc' => 'Wünsche zur Betreuung festhalten.', 'ready' => false ),
				array( 'label' => 'Anordnung für den Todesfall', 'slug' => 'anordnung-todesfall', 'desc' => 'Bestattung und Letztes regeln.', 'ready' => false ),
			),
		),
		array(
			'slug'     => 'musterbriefe',
			'label'    => 'Musterbriefe',
			'nav'      => 'Musterbriefe',
			'tagline'  => 'Wenn ein klarer Brief mehr bewegt als ein langes Gespräch.',
			'icon'     => 'letter',
			'template' => 'category',
			'featured' => 'maengelruege',
			'items'    => array(
				array( 'label' => 'Mängelrüge', 'slug' => 'maengelruege', 'desc' => 'Mängel fristgerecht und beweisbar melden.', 'ready' => true ),
				array( 'label' => 'Einsprache Bussenverfügung', 'slug' => 'einsprache-busse', 'desc' => 'Gegen eine Busse Einsprache erheben.', 'ready' => false ),
				array( 'label' => 'Mietzins anfechten', 'slug' => 'mietzins-anfechten', 'desc' => 'Ungerechtfertigte Erhöhung anfechten.', 'ready' => false ),
				array( 'label' => 'Zahlungsaufforderung', 'slug' => 'zahlungsaufforderung', 'desc' => 'Offene Forderung höflich, aber klar einfordern.', 'ready' => false ),
				array( 'label' => 'Widerruf', 'slug' => 'widerruf', 'desc' => 'Vertrag innert Frist widerrufen.', 'ready' => false ),
				array( 'label' => 'Beschwerde', 'slug' => 'beschwerde', 'desc' => 'Sachlich und wirkungsvoll reklamieren.', 'ready' => false ),
			),
		),
		array(
			'slug'     => 'tools',
			'label'    => 'Tools',
			'nav'      => 'Tools',
			'tagline'  => 'Vier Rechner, die sofort rechnen — ohne Anmeldung.',
			'icon'     => 'calculator',
			'template' => 'tools',
			'featured' => null,
			'items'    => array(
				array( 'label' => 'Erbschaftsrechner', 'slug' => 'tools#erbschaft', 'desc' => 'Gesetzliche Erbteile und Pflichtteile berechnen.', 'ready' => true ),
				array( 'label' => 'Bussenrechner', 'slug' => 'tools#busse', 'desc' => 'Bussen für Tempo­überschreitungen abschätzen.', 'ready' => true ),
				array( 'label' => 'MwSt-Rechner', 'slug' => 'tools#mwst', 'desc' => 'Brutto/Netto mit Schweizer Sätzen.', 'ready' => true ),
				array( 'label' => 'Lohnrechner (13. ML)', 'slug' => 'tools#lohn', 'desc' => 'Anteiligen 13. Monatslohn berechnen.', 'ready' => true ),
			),
		),
		array(
			'slug'     => 'ratgeber',
			'label'    => 'Ratgeber',
			'nav'      => 'Ratgeber',
			'tagline'  => 'Verständlich erklärt: Ihre Rechte im Schweizer Alltag.',
			'icon'     => 'compass',
			'template' => 'ratgeber',
			'featured' => null,
			'items'    => array(),
		),
	);

	return $ia;
}

/**
 * Findet eine Kategorie per Slug.
 */
function rf_ia_category( $slug ) {
	foreach ( rf_ia() as $cat ) {
		if ( $cat['slug'] === $slug ) {
			return $cat;
		}
	}
	return null;
}

/**
 * Metadaten der voll gebauten Detailseiten (Dokument-Generatoren).
 * key = page slug. 'doc' steuert das Formular/Vorschau-Modul in JS.
 */
function rf_documents() {
	return array(
		'wohnung-kuendigen'  => array(
			'title'  => 'Wohnung kündigen',
			'parent' => 'kuendigungsschreiben',
			'doc'    => 'kuendigung-wohnung',
			'lead'   => 'Erstellen Sie ein fristgerechtes, rechtssicheres Kündigungsschreiben für Ihre Mietwohnung. Alles bleibt in Ihrem Browser.',
		),
		'arbeitsvertrag'     => array(
			'title'  => 'Arbeitsvertrag erstellen',
			'parent' => 'vertraege',
			'doc'    => 'arbeitsvertrag',
			'lead'   => 'Ein klarer Schweizer Arbeitsvertrag mit allen wichtigen Punkten — verständlich erklärt, Feld für Feld.',
		),
		'patientenverfuegung' => array(
			'title'  => 'Patientenverfügung erstellen',
			'parent' => 'vorsorgedokumente',
			'doc'    => 'patientenverfuegung',
			'lead'   => 'Halten Sie Ihre medizinischen Wünsche verbindlich fest — damit im Ernstfall nach Ihrem Willen gehandelt wird.',
		),
		'arbeitsstelle-kuendigen' => array(
			'title'  => 'Arbeitsstelle kündigen',
			'parent' => 'kuendigungsschreiben',
			'doc'    => 'kuendigung-arbeit',
			'lead'   => 'Kündigen Sie Ihr Arbeitsverhältnis ordentlich und fristgerecht — mit korrekter Anrede und Bitte um Bestätigung und Arbeitszeugnis.',
		),
		'auto-kaufvertrag' => array(
			'title'  => 'Auto-Kaufvertrag',
			'parent' => 'vertraege',
			'doc'    => 'auto-kaufvertrag',
			'lead'   => 'Ein Kaufvertrag für den privaten Verkauf eines Fahrzeugs ab Platz — klar geregelt, inklusive «gekauft wie gesehen».',
		),
		'darlehensvertrag' => array(
			'title'  => 'Darlehensvertrag',
			'parent' => 'vertraege',
			'doc'    => 'darlehensvertrag',
			'lead'   => 'Ein privates Darlehen sauber festhalten — mit Betrag, Zins und Rückzahlung.',
		),
		'untermietvertrag' => array(
			'title'  => 'Untermietvertrag',
			'parent' => 'vertraege',
			'doc'    => 'untermietvertrag',
			'lead'   => 'Zimmer oder Wohnung rechtssicher untervermieten — mit Mietzins und Dauer.',
		),
		'maengelruege' => array(
			'title'  => 'Mängelrüge',
			'parent' => 'musterbriefe',
			'doc'    => 'maengelruege',
			'lead'   => 'Melden Sie einen Mangel fristgerecht und beweisbar — mit klarer Frist zur Behebung.',
		),
	);
}
