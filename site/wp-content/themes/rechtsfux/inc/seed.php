<?php
/**
 * Seeder — legt Seiten, Detailseiten, Blog, Menüs & Optionen an. Idempotent.
 *
 * @package Rechtsfux
 */

defined( 'ABSPATH' ) || exit;

/**
 * Legt eine Seite an oder aktualisiert sie (per Slug identifiziert).
 */
function rf_ensure_page( $slug, $title, $template = '', $parent = 0, $content = '' ) {
	$existing = get_page_by_path( $parent ? ( get_post_field( 'post_name', $parent ) . '/' . $slug ) : $slug );
	$data     = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_parent'  => $parent,
		'post_content' => $content,
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		wp_update_post( $data );
		$id = $existing->ID;
	} else {
		$id = wp_insert_post( $data );
	}
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return $id;
}

/**
 * Legt einen Blog-Beitrag an (per Slug identifiziert).
 */
function rf_ensure_post( $slug, $title, $content, $excerpt = '', $category = 'Ratgeber' ) {
	$cat_id   = 0;
	$term     = term_exists( $category, 'category' );
	if ( ! $term ) {
		$term = wp_insert_term( $category, 'category' );
	}
	$cat_id   = is_array( $term ) ? (int) $term['term_id'] : 0;

	$existing = get_page_by_path( $slug, OBJECT, 'post' );
	$data     = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'post',
		'post_content' => $content,
		'post_excerpt' => $excerpt,
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		wp_update_post( $data );
		$id = $existing->ID;
	} else {
		$id = wp_insert_post( $data );
	}
	if ( $cat_id ) {
		wp_set_post_categories( $id, array( $cat_id ) );
	}
	return $id;
}

/**
 * Kompletter Seed-Durchlauf.
 */
function rf_seed() {
	$log = array();

	// 1) Startseite.
	$home_id = rf_ensure_page( 'startseite', 'Rechtsfux', 'templates/front.php', 0, '' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', 0 );
	$log[] = "home=$home_id";

	// 2) Kategorie-Hubs + Detailseiten.
	foreach ( rf_ia() as $cat ) {
		$tpl     = 'templates/' . ( 'category' === $cat['template'] ? 'category' : $cat['template'] ) . '.php';
		$cat_id  = rf_ensure_page( $cat['slug'], $cat['label'], $tpl, 0, '' );
		$log[]   = $cat['slug'] . "=$cat_id";
	}

	// 3) Voll gebaute Dokument-Detailseiten (Kinder ihrer Kategorie).
	foreach ( rf_documents() as $slug => $doc ) {
		$parent = get_page_by_path( $doc['parent'] );
		$pid    = rf_ensure_page( $slug, $doc['title'], 'templates/document.php', $parent ? $parent->ID : 0, '' );
		update_post_meta( $pid, 'rf_doc', $doc['doc'] );
		update_post_meta( $pid, 'rf_lead', $doc['lead'] );
		$log[]  = "doc:$slug=$pid";
	}

	// 4) Statische Seiten.
	rf_ensure_page( 'ueber-uns', 'Über uns', 'templates/about.php', 0, '' );
	rf_ensure_page( 'kontakt', 'Kontakt', 'templates/contact.php', 0, '' );
	rf_ensure_page( 'impressum', 'Impressum', '', 0, rf_legal_impressum() );
	rf_ensure_page( 'datenschutz', 'Datenschutz', '', 0, rf_legal_datenschutz() );
	rf_ensure_page( 'agb', 'AGB', '', 0, rf_legal_agb() );

	// 5) Blog-Beiträge (1 voll + 2 Teaser).
	rf_seed_posts();

	// 6) Optionen.
	update_option( 'blogname', 'Rechtsfux' );
	update_option( 'blogdescription', 'Das clevere Schweizer Vertrags-Werkzeug' );
	update_option( 'timezone_string', 'Europe/Zurich' );
	update_option( 'date_format', 'j. F Y' );
	update_option( 'start_of_week', 1 );

	// 7) Permalinks auf /%postname%/ und Rewrite flushen.
	global $wp_rewrite;
	update_option( 'permalink_structure', '/%postname%/' );
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules( true );

	// 8) Primär-Menü aus IA aufbauen (für wp-admin-Bearbeitung; Header rendert eigenständig).
	rf_build_menu();

	return $log;
}

/**
 * Baut das Primärmenü.
 */
function rf_build_menu() {
	$menu_name = 'Hauptnavigation';
	$menu      = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
	} else {
		$menu_id = $menu->term_id;
		// Vorhandene Items leeren für sauberen Rebuild.
		foreach ( wp_get_nav_menu_items( $menu_id ) as $it ) {
			wp_delete_post( $it->ID, true );
		}
	}
	foreach ( rf_ia() as $cat ) {
		$page = get_page_by_path( $cat['slug'] );
		if ( $page ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $cat['nav'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}
	}
	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Blog-Beiträge.
 */
function rf_seed_posts() {
	rf_ensure_post(
		'elektronische-signatur-schweiz',
		'Elektronische Signatur: Wann sie in der Schweiz gültig ist',
		rf_article_esignature(),
		'Nicht jede digitale Unterschrift ist rechtsgültig. Wir erklären die drei Stufen der elektronischen Signatur — und wann welche zählt.',
		'Digitales'
	);
	rf_ensure_post(
		'mietzins-anfechten-fristen',
		'Mietzins anfechten: Diese Fristen sollten Sie kennen',
		rf_article_placeholder( 'Mietzins anfechten', 'Eine Mietzinserhöhung flattert ins Haus — und jetzt? Sie haben 30 Tage Zeit, um sie bei der Schlichtungsbehörde anzufechten.' ),
		'Eine Mietzinserhöhung im Briefkasten? Sie haben 30 Tage, um zu reagieren. So gehen Sie vor.',
		'Wohnen'
	);
	rf_ensure_post(
		'kuendigungsfristen-arbeitsvertrag',
		'Kündigungsfristen im Arbeitsvertrag — einfach erklärt',
		rf_article_placeholder( 'Kündigungsfristen', 'Die Kündigungsfrist hängt von den Dienstjahren ab. Im ersten Jahr gilt ein Monat, danach werden es zwei — sofern der Vertrag nichts anderes sagt.' ),
		'Ein Monat, zwei Monate, drei? Welche Frist bei einer Kündigung gilt, hängt vor allem von den Dienstjahren ab.',
		'Arbeit'
	);
}

/* ---------- Inhalts-Bausteine (Platzhalter, klar gekennzeichnet) ---------- */

function rf_article_esignature() {
	return <<<HTML
<p class="rf-lead">Ein Vertrag per E-Mail, ein Klick auf «Ich stimme zu», eine eingescannte Unterschrift: Digital unterschrieben ist schnell. Ob das auch <strong>rechtsgültig</strong> ist, hängt aber von der Art der Signatur ab. In der Schweiz kennt das Gesetz drei Stufen.</p>

<h2>Die einfache elektronische Signatur (EES)</h2>
<p>Das ist die niedrigste Stufe: eine getippte Namenszeile, ein Bild der Unterschrift, ein Klick. Sie ist praktisch für Alltägliches — etwa eine formlose Bestellung — hat aber vor Gericht wenig Beweiskraft, weil sich kaum belegen lässt, wer wirklich unterschrieben hat.</p>

<h2>Die fortgeschrittene elektronische Signatur (FES)</h2>
<p>Hier wird die Identität stärker abgesichert, und die Signatur ist mit dem Dokument so verknüpft, dass nachträgliche Änderungen auffallen. Für viele Verträge reicht das aus.</p>

<h2>Die qualifizierte elektronische Signatur (QES)</h2>
<p>Nur die QES ist der eigenhändigen Unterschrift rechtlich gleichgestellt. Sie beruht auf einem Zertifikat eines anerkannten Anbieters und einer geprüften Identität. Überall dort, wo das Gesetz die <em>Schriftform</em> verlangt, führt an ihr kein Weg vorbei.</p>

<h2>Was heisst das für Sie?</h2>
<p>Für die meisten Dokumente auf Rechtsfux — Kündigungen, Musterbriefe — genügt der Ausdruck mit handschriftlicher Unterschrift oder eine einfache elektronische Signatur. Sobald ein Gesetz ausdrücklich die Schriftform verlangt, brauchen Sie die QES oder das Papier mit Ihrer Unterschrift.</p>

<p><em>Dieser Beitrag dient der allgemeinen Information und ersetzt keine Rechtsberatung im Einzelfall.</em></p>
HTML;
}

function rf_article_placeholder( $topic, $intro ) {
	return "<p class=\"rf-lead\">{$intro}</p>\n<p>Dieser Ratgeber-Beitrag ist Teil der Rechtsfux-Beispielinhalte. Der vollständige Text zu «{$topic}» wird hier ausgearbeitet und erklärt Schritt für Schritt Ihre Rechte, Fristen und das konkrete Vorgehen.</p>\n<h2>Das Wichtigste in Kürze</h2>\n<ul><li>Reagieren Sie fristgerecht — verpasste Fristen lassen sich selten heilen.</li><li>Halten Sie alles schriftlich fest.</li><li>Nutzen Sie eine passende Vorlage von Rechtsfux als Ausgangspunkt.</li></ul>\n<p><em>Dieser Beitrag dient der allgemeinen Information und ersetzt keine Rechtsberatung im Einzelfall.</em></p>";
}

function rf_legal_impressum() {
	return "<p><strong>Hinweis:</strong> Dies ist eine Demonstrationswebsite mit Platzhalter-Angaben. Rechtsfux ist eine fiktive Marke; die folgenden Angaben sind Platzhalter.</p><h2>Betreiber</h2><p>Rechtsfux (Demo)<br>Musterstrasse 1<br>8000 Zürich<br>Schweiz</p><h2>Kontakt</h2><p>E-Mail: hallo@rechtsfux.example</p>";
}

function rf_legal_datenschutz() {
	return "<p><strong>Hinweis:</strong> Platzhalter-Datenschutzerklärung einer Demonstrationswebsite.</p><p>Alle in den Dokument-Generatoren eingegebenen Daten bleiben ausschliesslich in Ihrem Browser. Es findet keine Übermittlung an einen Server statt.</p>";
}

function rf_legal_agb() {
	return "<p><strong>Hinweis:</strong> Platzhalter-AGB einer Demonstrationswebsite.</p><p>Die Vorlagen und Rechner dienen der allgemeinen Orientierung und ersetzen keine individuelle Rechtsberatung.</p>";
}
