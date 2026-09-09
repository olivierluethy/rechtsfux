<?php
/**
 * Einzelner Beitrag (Artikel).
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
	$cats = get_the_category();
	$tag  = $cats ? $cats[0]->name : 'Ratgeber';
	?>
	<article>
		<div class="rf-article__head rf-pagehead" style="padding-bottom:.5rem;">
			<div class="rf-hero__grid" aria-hidden="true" style="mask-image:radial-gradient(120% 80% at 85% 0,#000,transparent 55%);"></div>
			<div class="rf-container rf-narrow" style="position:relative;">
				<nav class="rf-breadcrumb" aria-label="Brotkrumen">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start</a>
					<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
					<a href="<?php echo esc_url( rf_url( 'ratgeber' ) ); ?>">Ratgeber</a>
				</nav>
				<span class="rf-tag"><?php echo esc_html( $tag ); ?></span>
				<h1 class="rf-article__title" style="margin-top:.8rem;"><?php the_title(); ?></h1>
				<div class="rf-article__meta">
					<?php echo rf_icon( 'clock' ); // phpcs:ignore ?>
					<span><?php echo esc_html( get_the_date() ); ?></span>
					<span>·</span>
					<span><?php echo esc_html( rf_reading_time( get_the_content() ) ); ?> Min. Lesezeit</span>
				</div>
			</div>
		</div>

		<div class="rf-section" style="padding-top:1.5rem;">
			<div class="rf-container rf-narrow">
				<div class="rf-prose"><?php the_content(); ?></div>

				<div style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid var(--line);">
					<a class="rf-btn rf-btn--ghost" href="<?php echo esc_url( rf_url( 'ratgeber' ) ); ?>">← Alle Beiträge</a>
				</div>
			</div>
		</div>
	</article>
	<?php
endwhile;
get_footer();
