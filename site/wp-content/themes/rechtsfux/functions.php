<?php
/**
 * Rechtsfux Theme — Setup & Helfer.
 *
 * @package Rechtsfux
 */

defined( 'ABSPATH' ) || exit;

define( 'RF_DIR', get_template_directory() );
define( 'RF_URI', get_template_directory_uri() );
define( 'RF_VER', '1.0.0' );

require_once RF_DIR . '/inc/ia.php';
require_once RF_DIR . '/inc/icons.php';
require_once RF_DIR . '/inc/doc-forms.php';
require_once RF_DIR . '/inc/seed.php';

/**
 * Theme-Support.
 */
function rf_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => 'Hauptnavigation',
			'footer'  => 'Footer',
		)
	);
}
add_action( 'after_setup_theme', 'rf_setup' );

/**
 * Styles & Scripts.
 */
function rf_assets() {
	// Google Fonts: Bricolage Grotesque (Display) + Hanken Grotesk (Text).
	wp_enqueue_style(
		'rf-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Hanken+Grotesk:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	$css = RF_DIR . '/assets/css/main.css';
	wp_enqueue_style( 'rf-main', RF_URI . '/assets/css/main.css', array( 'rf-fonts' ), file_exists( $css ) ? filemtime( $css ) : RF_VER );

	$main = RF_DIR . '/assets/js/main.js';
	wp_enqueue_script( 'rf-main', RF_URI . '/assets/js/main.js', array(), file_exists( $main ) ? filemtime( $main ) : RF_VER, true );

	// Nur wo gebraucht: Rechner auf Tools, Vorschau auf Dokumentseiten.
	if ( is_page_template( 'templates/tools.php' ) ) {
		$c = RF_DIR . '/assets/js/calculators.js';
		wp_enqueue_script( 'rf-calc', RF_URI . '/assets/js/calculators.js', array(), file_exists( $c ) ? filemtime( $c ) : RF_VER, true );
	}
	if ( is_page_template( 'templates/document.php' ) ) {
		$d = RF_DIR . '/assets/js/document-preview.js';
		wp_enqueue_script( 'rf-doc', RF_URI . '/assets/js/document-preview.js', array(), file_exists( $d ) ? filemtime( $d ) : RF_VER, true );
	}
}
add_action( 'wp_enqueue_scripts', 'rf_assets' );

/**
 * Preconnect zu Google Fonts.
 */
function rf_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'rf_resource_hints', 10, 2 );

/**
 * Favicon, App-Icons und Web-Manifest ausgeben.
 */
function rf_favicons() {
	$a = RF_URI . '/assets';
	printf( '<link rel="icon" type="image/svg+xml" href="%s">' . "\n", esc_url( $a . '/favicon.svg' ) );
	printf( '<link rel="icon" type="image/png" sizes="32x32" href="%s">' . "\n", esc_url( $a . '/favicon-32.png' ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $a . '/apple-touch-icon.png' ) );
	printf( '<link rel="manifest" href="%s">' . "\n", esc_url( $a . '/site.webmanifest' ) );
}
add_action( 'wp_head', 'rf_favicons', 3 );

/**
 * Open-Graph-, Twitter- und Meta-Description-Tags für Teilen & SEO.
 */
function rf_open_graph() {
	$title = wp_get_document_title();
	$desc  = get_bloginfo( 'description' );
	if ( is_singular( 'post' ) ) {
		$ex = get_the_excerpt();
		if ( $ex ) {
			$desc = $ex;
		}
	}
	$desc = trim( mb_substr( wp_strip_all_tags( $desc ), 0, 200 ) );
	$url  = is_singular() ? get_permalink() : home_url( '/' );
	$img  = RF_URI . '/assets/og-image.png';

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:site_name" content="Rechtsfux">' . "\n";
	echo '<meta property="og:locale" content="de_CH">' . "\n";
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $img ) );
}
add_action( 'wp_head', 'rf_open_graph', 4 );

/**
 * robots.txt sinnvoll ergänzen. Den Sitemap-Verweis fügt WordPress-Core
 * (seit 5.5) bereits selbst hinzu — wir ergänzen nur Disallow-Regeln.
 */
function rf_robots_txt( $output, $public ) {
	if ( '0' === (string) $public ) {
		return $output; // Site nicht öffentlich — nichts ergänzen.
	}
	$output .= "Disallow: /wp-login.php\n";
	$output .= "Disallow: /*?s=\n"; // interne Suchergebnisse nicht indexieren
	return $output;
}
add_filter( 'robots_txt', 'rf_robots_txt', 10, 2 );

/**
 * URL einer Seite anhand ihres Slugs (Kind- und Top-Level).
 */
function rf_url( $slug ) {
	// Anker (z.B. tools#busse) direkt behandeln.
	if ( false !== strpos( $slug, '#' ) ) {
		list( $base, $anchor ) = explode( '#', $slug, 2 );
		return rf_url( $base ) . '#' . $anchor;
	}
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	// Fallback: Kind einer Kategorie
	foreach ( rf_ia() as $cat ) {
		$maybe = get_page_by_path( $cat['slug'] . '/' . $slug );
		if ( $maybe ) {
			return get_permalink( $maybe );
		}
	}
	return home_url( '/' . ltrim( $slug, '/' ) . '/' );
}

/**
 * Ziel-URL eines IA-Items: eigene Seite, wenn ready, sonst Hub-Seite.
 */
function rf_item_url( $item, $cat ) {
	if ( ! empty( $item['ready'] ) ) {
		return rf_url( $item['slug'] );
	}
	return rf_url( $cat['slug'] );
}

/**
 * Titel + optionaler Lead für Kategorie-Templates.
 */
function rf_current_category() {
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	return rf_ia_category( $slug );
}

/**
 * Leseminuten grob schätzen.
 */
function rf_reading_time( $content ) {
	$words = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * Body-Klassen für Feinsteuerung.
 */
function rf_body_class( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-front';
	}
	return $classes;
}
add_filter( 'body_class', 'rf_body_class' );

/**
 * Excerpt-Länge/-More entschärfen.
 */
add_filter( 'excerpt_length', function () { return 28; } );
add_filter( 'excerpt_more', function () { return '…'; } );
