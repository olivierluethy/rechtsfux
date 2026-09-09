<?php
/**
 * Template Name: Ratgeber
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
			<span>Ratgeber</span>
		</nav>
		<div class="rf-pagehead__ico"><?php echo rf_icon( 'compass' ); // phpcs:ignore ?></div>
		<h1>Ratgeber</h1>
		<p class="rf-sub">Verständlich erklärt: Ihre Rechte und Pflichten im Schweizer Alltag.</p>
	</div>
</section>

<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container">
		<div class="rf-posts">
			<?php
			$q = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 12, 'ignore_sticky_posts' => true ) );
			if ( $q->have_posts() ) :
				while ( $q->have_posts() ) : $q->the_post();
					$cats = get_the_category();
					$tag  = $cats ? $cats[0]->name : 'Ratgeber';
					?>
					<article class="rf-post rf-reveal">
						<div class="rf-post__thumb"><?php echo rf_icon( 'compass' ); // phpcs:ignore ?></div>
						<div class="rf-post__body">
							<div class="rf-post__meta"><span class="rf-tag"><?php echo esc_html( $tag ); ?></span><span><?php echo esc_html( get_the_date() ); ?></span></div>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
						<a class="rf-post__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</article>
					<?php
				endwhile;
			endif;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php get_footer(); ?>
