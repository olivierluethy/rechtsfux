<?php
/**
 * Router für den PHP-Built-in-Server → WordPress.
 * - Vorhandene Dateien werden direkt ausgeliefert (statisch oder PHP wie wp-login.php).
 * - Vorhandene Verzeichnisse (z. B. /wp-admin/) liefern ihr index.php aus.
 * - Alles andere (pretty permalinks) geht an den WordPress-Front-Controller.
 */

$path = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$full = __DIR__ . $path;

if ( '/' !== $path && file_exists( $full ) ) {
	if ( is_dir( $full ) ) {
		// Verzeichnis: dessen index.php ausführen (z. B. wp-admin/index.php).
		$index = rtrim( $full, '/' ) . '/index.php';
		if ( file_exists( $index ) ) {
			$script                        = rtrim( $path, '/' ) . '/index.php';
			$_SERVER['SCRIPT_NAME']        = $script;
			$_SERVER['PHP_SELF']           = $script;
			$_SERVER['SCRIPT_FILENAME']    = $index;
			chdir( dirname( $index ) );
			require $index;
			return true;
		}
		// Kein index.php im Verzeichnis → an WordPress weiterreichen.
	} else {
		// Vorhandene Datei (Bild, CSS, JS, wp-login.php …) direkt ausliefern.
		return false;
	}
}

// WordPress-Front-Controller.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';
require __DIR__ . '/index.php';
