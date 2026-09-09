<?php
// Router für PHP built-in server -> WordPress pretty permalinks
$uri = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$file = __DIR__ . $uri;
if ( $uri !== '/' && file_exists( $file ) && ! is_dir( $file ) ) {
	return false; // serve the static/existing file as-is
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
