<?php
/**
 * 404 — Seite nicht gefunden.
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="rf-section" style="text-align:center;padding-block:clamp(4rem,3rem+6vw,8rem);">
	<div class="rf-container rf-narrow">
		<div style="display:inline-flex;margin:0 auto 1.5rem;"><?php echo rf_fox_mark( 72 ); // phpcs:ignore ?></div>
		<h1>Der Fux hat sich verlaufen</h1>
		<p class="rf-sub" style="margin:0 auto 1.75rem;">Diese Seite gibt es nicht (mehr). Vielleicht finden Sie über die Startseite oder die Suche, was Sie brauchen.</p>
		<div style="display:flex;gap:.8rem;justify-content:center;flex-wrap:wrap;">
			<a class="rf-btn rf-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Zur Startseite</a>
			<a class="rf-btn rf-btn--ghost" href="<?php echo esc_url( rf_url( 'kuendigungsschreiben' ) ); ?>">Dokument erstellen</a>
		</div>
	</div>
</section>
<?php get_footer(); ?>
