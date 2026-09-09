<?php
/**
 * Rechtsfux — lokale WordPress-Konfiguration (SQLite)
 */

// --- SQLite: keine echten MySQL-Zugangsdaten nötig, aber Konstanten müssen definiert sein ---
define( 'DB_NAME', 'rechtsfux' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// SQLite-Speicherort
define( 'DB_DIR', dirname( __FILE__ ) . '/wp-content/database/' );
define( 'DB_FILE', 'rechtsfux.sqlite' );

// --- Sicherheits-Salts ---
define('AUTH_KEY',         '$Q+6!|4|BU{91^Xi|!j(Ypzkl/#4;R2A)/p }eV-ez0|4~Hre3(Xe_|(QkBEQ`gX');
define('SECURE_AUTH_KEY',  'h|y~&9|j-IfW|%O0,b,>E5Ih*bag9Y+}Z|6N2LZj#M|K2 f~u0/K)1jnKvn,eqgi');
define('LOGGED_IN_KEY',    'm,-f0/9QQ1j}X]xYV8MlSzH.h{*_c~cf~c(m-p^#c4M_&O_|e6:6AG29^JnM(LTA');
define('NONCE_KEY',        'VO!qi(C$h#a6<34=NX:AQ7HJWTW-fi/k1)9$M;UR!#-.:bud1&hqYf6Zm/kn%y~M');
define('AUTH_SALT',        '6&+uh.j@6z:W#fMK+f!Arg(=3n=(WKl3h(n^r)p$L^}_-^{z<&E6<!D8+M0-w&k;');
define('SECURE_AUTH_SALT', 'U4f;i1T~vaU!>hsoy=F623?Wu4viMjdb:tsfq1)o:wE+YRK}#w^SD]GD 1f4tC<n');
define('LOGGED_IN_SALT',   '%9{.FvGt18<%{|u!<GgMso-T#i.6x7l@v11vgllB-4o#f-G.m5(Ua!X?Gz=CAwhv');
define('NONCE_SALT',       '!kqUb`sg&otB ?){#^Zd;sC7r*{,javPprz+_#X-*%si4#PTh -/=6.,2[tRtG|C');

// --- Tabellen-Präfix ---
$table_prefix = 'rf_';

// --- Lokale URLs ---
define( 'WP_HOME', 'http://localhost:8080' );
define( 'WP_SITEURL', 'http://localhost:8080' );

// --- Entwicklung ---
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'AUTOSAVE_INTERVAL', 300 );
define( 'WP_POST_REVISIONS', 3 );
define( 'DISALLOW_FILE_EDIT', false );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
