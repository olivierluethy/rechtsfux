<?php
/**
 * Fallback-Template (Blog / Archiv / Suche).
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="rf-pagehead">
	<div class="rf-container">
		<h1><?php echo is_search() ? esc_html( 'Suche: ' . get_search_query() ) : esc_html( single_post_title( '', false ) ?: 'Ratgeber' ); ?></h1>
	</div>
</section>
<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container">
		<?php if ( have_posts() ) : ?>
			<div class="rf-posts">
				<?php while ( have_posts() ) : the_post();
					$cats = get_the_category();
					$tag  = $cats ? $cats[0]->name : 'Ratgeber'; ?>
					<article class="rf-post">
						<div class="rf-post__thumb"><?php echo rf_icon( 'compass' ); // phpcs:ignore ?></div>
						<div class="rf-post__body">
							<div class="rf-post__meta"><span class="rf-tag"><?php echo esc_html( $tag ); ?></span><span><?php echo esc_html( get_the_date() ); ?></span></div>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
						<a class="rf-post__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</article>
				<?php endwhile; ?>
			</div>
			<div style="margin-top:2rem;"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
		<?php else : ?>
			<p class="rf-sub">Keine Beiträge gefunden.</p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
