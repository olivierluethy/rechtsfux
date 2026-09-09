<?php
/**
 * Footer.
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
$ia = rf_ia();
?>
</main>

<footer class="rf-footer">
	<div class="rf-container">
		<div class="rf-footer__top">
			<div class="rf-footer__brand">
				<?php echo rf_logo(); // phpcs:ignore ?>
				<p>Das clevere Schweizer Vertrags-Werkzeug. Kündigungen, Verträge und Vorsorge­dokumente — verständlich, rechtssicher, in Minuten. Ihre Daten bleiben in Ihrem Browser.</p>
			</div>

			<div class="rf-footer__col">
				<h4>Dokumente</h4>
				<ul>
					<?php foreach ( array_slice( $ia, 0, 4 ) as $cat ) : ?>
						<li><a href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><?php echo esc_html( $cat['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="rf-footer__col">
				<h4>Rechtsfux</h4>
				<ul>
					<li><a href="<?php echo esc_url( rf_url( 'tools' ) ); ?>">Rechner &amp; Tools</a></li>
					<li><a href="<?php echo esc_url( rf_url( 'ratgeber' ) ); ?>">Ratgeber</a></li>
					<li><a href="<?php echo esc_url( rf_url( 'ueber-uns' ) ); ?>">Über uns</a></li>
					<li><a href="<?php echo esc_url( rf_url( 'kontakt' ) ); ?>">Kontakt</a></li>
				</ul>
			</div>

			<div class="rf-footer__col">
				<h4>Rechtliches</h4>
				<ul>
					<li><a href="<?php echo esc_url( rf_url( 'impressum' ) ); ?>">Impressum</a></li>
					<li><a href="<?php echo esc_url( rf_url( 'datenschutz' ) ); ?>">Datenschutz</a></li>
					<li><a href="<?php echo esc_url( rf_url( 'agb' ) ); ?>">AGB</a></li>
				</ul>
			</div>
		</div>

		<div class="rf-footer__bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> Rechtsfux — Demo. Keine Rechtsberatung im Einzelfall.</span>
			<span class="rf-footer__swiss"><?php echo rf_icon( 'flag' ); // phpcs:ignore ?> Für die Schweiz gemacht</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
