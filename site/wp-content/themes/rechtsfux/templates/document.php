<?php
/**
 * Template Name: Dokument-Generator
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();

$doc_type = get_post_meta( get_the_ID(), 'rf_doc', true );
$lead     = get_post_meta( get_the_ID(), 'rf_lead', true );
$parent   = wp_get_post_parent_id( get_the_ID() );
$cat      = $parent ? rf_ia_category( get_post_field( 'post_name', $parent ) ) : null;
?>

<section class="rf-pagehead">
	<div class="rf-hero__grid" aria-hidden="true" style="mask-image:radial-gradient(120% 80% at 85% 0,#000,transparent 55%);"></div>
	<div class="rf-container" style="position:relative;">
		<nav class="rf-breadcrumb" aria-label="Brotkrumen">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start</a>
			<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
			<?php if ( $cat ) : ?>
				<a href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><?php echo esc_html( $cat['label'] ); ?></a>
				<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
			<?php endif; ?>
			<span><?php the_title(); ?></span>
		</nav>
		<div class="rf-pagehead__ico"><?php echo rf_icon( 'pen' ); // phpcs:ignore ?></div>
		<h1><?php the_title(); ?></h1>
		<?php if ( $lead ) : ?><p class="rf-sub"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
	</div>
</section>

<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container">
		<?php rf_render_document( $doc_type ); ?>
	</div>
</section>

<?php get_footer(); ?>
