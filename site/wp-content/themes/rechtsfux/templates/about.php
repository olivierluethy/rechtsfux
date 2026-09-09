<?php
/**
 * Template Name: Über uns
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
			<span>Über uns</span>
		</nav>
		<span class="rf-tick"><?php echo rf_icon( 'spark' ); // phpcs:ignore ?> Die Idee hinter dem Fux</span>
		<h1>Recht muss nicht kompliziert sein</h1>
		<p class="rf-sub" style="max-width:56ch;">Die meisten Rechtsthemen im Alltag sind Standardfälle. Trotzdem kosten sie Zeit, Nerven — oder viel Geld. Rechtsfux macht sie einfach.</p>
	</div>
</section>

<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container">
		<div class="rf-split">
			<div class="rf-split__text rf-prose">
				<h2>Was uns antreibt</h2>
				<p>Ein Kündigungsschreiben, ein einfacher Vertrag, eine Patientenverfügung: Dafür braucht es selten eine Kanzlei. Es braucht die richtige Vorlage, verständlich erklärt, und die Gewissheit, dass alles korrekt ist.</p>
				<p>Genau das liefert Rechtsfux — schnell, sauber und ohne dass Ihre Daten das Gerät verlassen. Wo es doch komplizierter wird, sagen wir es ehrlich und verweisen auf die passende Beratung.</p>
				<h2>Wie wir arbeiten</h2>
				<p>Unsere Vorlagen orientieren sich am Schweizer Recht und am gesunden Menschenverstand. Wir schreiben so, dass man es versteht — nicht so, dass man beeindruckt ist.</p>
			</div>
			<div class="rf-split__media">
				<div class="rf-panel">
					<ul class="rf-mini-list">
						<li><?php echo rf_icon( 'check' ); // phpcs:ignore ?> <span><strong>Verständlich.</strong> Jedes Feld wird erklärt.</span></li>
						<li><?php echo rf_icon( 'check' ); // phpcs:ignore ?> <span><strong>Privat.</strong> Ihre Eingaben bleiben im Browser.</span></li>
						<li><?php echo rf_icon( 'check' ); // phpcs:ignore ?> <span><strong>Schweizerisch.</strong> Auf hiesiges Recht abgestimmt.</span></li>
						<li><?php echo rf_icon( 'check' ); // phpcs:ignore ?> <span><strong>Ehrlich.</strong> Wir sagen, wo Beratung nötig ist.</span></li>
					</ul>
					<div class="rf-note" style="margin-top:1.25rem;"><?php echo rf_icon( 'shield' ); // phpcs:ignore ?><span>Rechtsfux ist eine Demonstrations-Website mit einer fiktiven Marke. Sie ersetzt keine Rechtsberatung im Einzelfall.</span></div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>
