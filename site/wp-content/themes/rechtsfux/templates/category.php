<?php
/**
 * Template Name: Kategorie
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
get_header();

$cat = rf_current_category();
if ( ! $cat ) { $cat = array( 'label' => get_the_title(), 'tagline' => '', 'icon' => 'contract', 'items' => array(), 'featured' => null, 'slug' => '' ); }
$featured_url = $cat['featured'] ? rf_url( $cat['featured'] ) : '';
?>

<section class="rf-pagehead">
	<div class="rf-hero__grid" aria-hidden="true" style="mask-image:radial-gradient(120% 80% at 85% 0,#000,transparent 55%);"></div>
	<div class="rf-container" style="position:relative;">
		<nav class="rf-breadcrumb" aria-label="Brotkrumen">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start</a>
			<?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
			<span><?php echo esc_html( $cat['label'] ); ?></span>
		</nav>
		<div class="rf-pagehead__ico"><?php echo rf_icon( $cat['icon'] ); // phpcs:ignore ?></div>
		<h1><?php echo esc_html( $cat['label'] ); ?></h1>
		<p class="rf-sub"><?php echo esc_html( $cat['tagline'] ); ?></p>
	</div>
</section>

<section class="rf-section rf-section--tight" style="padding-top:0;">
	<div class="rf-container">
		<?php if ( $featured_url ) :
			$fdoc = rf_documents()[ $cat['featured'] ] ?? null; ?>
			<a class="rf-panel rf-reveal" href="<?php echo esc_url( $featured_url ); ?>" style="display:flex;gap:1.25rem;align-items:center;text-decoration:none;color:inherit;border-color:var(--accent);">
				<span class="rf-cat__ico" style="flex:none;width:56px;height:56px;"><?php echo rf_icon( 'pen' ); // phpcs:ignore ?></span>
				<span style="flex:1;">
					<span class="rf-mega__badge" style="display:inline-block;margin-bottom:.4rem;">Jetzt verfügbar</span>
					<span style="display:block;font-family:var(--font-display);font-weight:700;font-size:1.25rem;color:var(--ink);"><?php echo esc_html( $fdoc['title'] ?? $cat['label'] ); ?></span>
					<span style="display:block;color:var(--ink-2);font-size:.95rem;margin-top:.2rem;"><?php echo esc_html( $fdoc['lead'] ?? 'Dokument mit Live-Vorschau erstellen.' ); ?></span>
				</span>
				<span class="rf-btn rf-btn--primary" style="flex:none;">Erstellen <?php echo rf_icon( 'arrow' ); // phpcs:ignore ?></span>
			</a>
		<?php endif; ?>

		<div class="rf-itemgrid">
			<?php foreach ( $cat['items'] as $item ) :
				$ready = ! empty( $item['ready'] );
				$url   = $ready ? rf_url( $item['slug'] ) : ( $featured_url ? $featured_url : '' );
				?>
				<article class="rf-item rf-reveal">
					<div class="rf-item__head">
						<h3><?php echo esc_html( $item['label'] ); ?></h3>
						<?php if ( $ready ) : ?>
							<span class="rf-item__badge">live</span>
						<?php else : ?>
							<span class="rf-item__soon">bald</span>
						<?php endif; ?>
					</div>
					<p><?php echo esc_html( $item['desc'] ); ?></p>
					<?php if ( $url ) : ?>
						<a class="rf-item__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="rf-note" style="margin-top:2rem;">
			<?php echo rf_icon( 'shield' ); // phpcs:ignore ?>
			<span>Diese Vorlagen sind auf Schweizer Recht abgestimmt und für den Normalfall gedacht. Bei besonderen Situationen ziehen Sie zusätzlich eine Rechtsberatung bei. Rechtsfux ist eine Demo und ersetzt keine anwaltliche Beratung im Einzelfall.</span>
		</div>
	</div>
</section>

<?php get_footer(); ?>
