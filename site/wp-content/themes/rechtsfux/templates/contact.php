<?php
/**
 * Template Name: Kontakt
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="rf-pagehead">
	<div class="rf-hero__grid" aria-hidden="true" style="mask-image:radial-gradient(120% 80% at 85% 0,#000,transparent 55%);"></div>
	<div class="rf-container" style="position:relative;">
		<nav class="rf-breadcrumb" aria-label="Brotkrumen">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start</a>
			<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
			<span>Kontakt</span>
		</nav>
		<div class="rf-pagehead__head">
			<div class="rf-pagehead__ico"><?php echo rf_icon( 'letter' ); // phpcs:ignore ?></div>
			<h1>Sagen Sie Hallo</h1>
		</div>
		<p class="rf-sub">Eine Frage, ein Hinweis, ein Wunsch für eine neue Vorlage? Schreiben Sie uns.</p>
	</div>
</section>

<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container">
		<div class="rf-split">
			<div class="rf-split__text">
				<div class="rf-panel">
					<form class="rf-form" onsubmit="event.preventDefault(); this.reset(); document.getElementById('rf-contact-done').hidden=false;">
						<div class="rf-field-row">
							<div class="rf-field"><label>Name</label><input class="rf-input" required placeholder="Ihr Name"></div>
							<div class="rf-field"><label>E-Mail</label><input class="rf-input" type="email" required placeholder="name@example.ch"></div>
						</div>
						<div class="rf-field"><label>Betreff</label><input class="rf-input" placeholder="Worum geht es?"></div>
						<div class="rf-field"><label>Nachricht</label><textarea class="rf-textarea" required placeholder="Ihre Nachricht …"></textarea></div>
						<button class="rf-btn rf-btn--primary" type="submit"><?php echo rf_icon( 'letter' ); // phpcs:ignore ?> Nachricht senden</button>
						<p id="rf-contact-done" hidden style="color:var(--accent);font-weight:600;margin-top:.75rem;">Danke! In dieser Demo wird nichts versendet — aber das Formular funktioniert.</p>
					</form>
				</div>
			</div>
			<div class="rf-split__media">
				<div class="rf-prose">
					<h2>Direkt erreichbar</h2>
					<p><strong>E-Mail</strong><br>hallo@rechtsfux.example</p>
					<p><strong>Adresse</strong><br>Rechtsfux (Demo)<br>Musterstrasse 1<br>8000 Zürich</p>
					<p><strong>Erreichbarkeit</strong><br>Mo–Fr, 9–17 Uhr</p>
					<div class="rf-note"><?php echo rf_icon( 'lock' ); // phpcs:ignore ?><span>Dies ist eine Demonstrations-Website. Die Kontaktangaben sind Platzhalter.</span></div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>
