<?php
/**
 * Standard-Seite (z.B. Impressum, Datenschutz, AGB).
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
	?>
	<section class="rf-pagehead">
		<div class="rf-container rf-narrow">
			<nav class="rf-breadcrumb" aria-label="Brotkrumen">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start</a>
				<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
				<span><?php the_title(); ?></span>
			</nav>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>
	<section class="rf-section" style="padding-top:1rem;">
		<div class="rf-container rf-narrow">
			<div class="rf-prose"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
