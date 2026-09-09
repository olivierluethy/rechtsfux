<?php
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/wp-load.php';

// Theme-Funktionen direkt laden (Theme ist aktiv, aber functions.php lief in diesem Bootstrap nicht).
$theme_dir = get_theme_root() . '/rechtsfux';
if ( ! defined( 'RF_DIR' ) ) {
	require_once $theme_dir . '/functions.php';
}
if ( ! function_exists( 'rf_seed' ) ) {
	require_once $theme_dir . '/inc/ia.php';
	require_once $theme_dir . '/inc/seed.php';
}

$log = rf_seed();
echo "SEED OK: " . count( $log ) . " Seiten\n";
echo implode( "\n", $log ) . "\n";
