<?php
/**
 * Inline-SVG-Icons und die Rechtsfux-Wortmarke.
 *
 * @package Rechtsfux
 */

defined( 'ABSPATH' ) || exit;

/**
 * Geometrisches Fuchs-Logo. Nutzt currentColor + Akzent, damit es in beiden Themes stimmt.
 */
function rf_fox_mark( $size = 30 ) {
	$s = (int) $size;
	ob_start(); ?>
	<svg class="rf-fox" width="<?php echo esc_attr( $s ); ?>" height="<?php echo esc_attr( $s ); ?>" viewBox="0 0 32 32" fill="none" aria-hidden="true" focusable="false">
		<!-- Ohren -->
		<path d="M5 4 L13 9 L6 13 Z" fill="var(--accent)"/>
		<path d="M27 4 L19 9 L26 13 Z" fill="var(--accent)"/>
		<!-- Kopf -->
		<path d="M6 12 L16 8 L26 12 L22 22 L16 27 L10 22 Z" fill="currentColor"/>
		<!-- Gesichtsmaske -->
		<path d="M16 15 L21 13 L19 21 L16 24 L13 21 L11 13 Z" fill="var(--surface)"/>
		<!-- Schnauze -->
		<path d="M16 24 L13.5 20.5 L16 19.5 L18.5 20.5 Z" fill="currentColor"/>
		<!-- Augen -->
		<circle cx="13.4" cy="16.4" r="1.15" fill="var(--accent)"/>
		<circle cx="18.6" cy="16.4" r="1.15" fill="var(--accent)"/>
	</svg>
	<?php
	return ob_get_clean();
}

/**
 * Wortmarke mit Fuchs-Logo.
 */
function rf_logo() {
	ob_start(); ?>
	<a class="rf-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Rechtsfux — Startseite">
		<?php echo rf_fox_mark( 30 ); // phpcs:ignore ?>
		<span class="rf-logo__word">Rechts<span class="rf-logo__accent">fux</span></span>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * Icon-Bibliothek. Alle stroke-basiert, currentColor, 1.6 stroke.
 */
function rf_icon( $name, $class = '' ) {
	$paths = array(
		'termination' => '<path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4"/><path d="m9.5 12.5 5 5"/><path d="m14.5 12.5-5 5"/>',
		'contract'    => '<path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4"/><path d="M9.5 12h6"/><path d="M9.5 15.5h6"/><path d="M9.5 8.5h2.5"/>',
		'shield'      => '<path d="M12 3 5 6v5c0 4.2 2.9 7.7 7 9 4.1-1.3 7-4.8 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
		'letter'      => '<rect x="4" y="6" width="16" height="12" rx="1.5"/><path d="m4.5 7 7.5 5.5L19.5 7"/>',
		'calculator'  => '<rect x="6" y="3" width="12" height="18" rx="2"/><path d="M9 7h6"/><path d="M9 11h.01M12 11h.01M15 11h.01M9 14h.01M12 14h.01M15 14h.01M9 17h.01M12 17h.01"/>',
		'compass'     => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
		'search'      => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.7-3.7"/>',
		'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'moon'        => '<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z"/>',
		'arrow'       => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
		'arrow-up'    => '<path d="M12 19V5"/><path d="m6 11 6-6 6 6"/>',
		'check'       => '<path d="m5 12 4.5 4.5L19 7"/>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'       => '<path d="m6 6 12 12M18 6 6 18"/>',
		'print'       => '<path d="M7 8V3h10v5"/><rect x="4" y="8" width="16" height="8" rx="1.5"/><path d="M7 14h10v6H7z"/>',
		'copy'        => '<rect x="9" y="9" width="11" height="11" rx="2"/><path d="M6 15H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"/>',
		'chevron'     => '<path d="m8 10 4 4 4-4"/>',
		'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'lock'        => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
		'flag'        => '<path d="M6 21V4"/><path d="M6 4h11l-2 4 2 4H6"/>',
		'spark'       => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18"/>',
		'download'    => '<path d="M12 4v10"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/>',
		'pen'         => '<path d="M4 20h4L18.5 9.5a2 2 0 0 0-3-3L5 17z"/><path d="M14.5 7.5l3 3"/>',
		'scale'       => '<path d="M12 4v16"/><path d="M7 20h10"/><path d="M4 8h16"/><path d="m4 8-2.2 5a3 3 0 0 0 5.4 0z"/><path d="m20 8-2.2 5a3 3 0 0 0 5.4 0z"/>',
	);

	$body = isset( $paths[ $name ] ) ? $paths[ $name ] : '';
	$cls  = trim( 'rf-icon ' . $class );

	return sprintf(
		'<svg class="%s" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $cls ),
		$body // phpcs:ignore
	);
}
