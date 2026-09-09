<?php
/**
 * Header + Navigation.
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0E7C66">
	<script>
		/* Theme vor dem Paint setzen — kein Flash. */
		(function () {
			try {
				var t = localStorage.getItem('rf-theme');
				if (t === 'dark' || t === 'light') {
					document.documentElement.setAttribute('data-theme', t);
				}
			} catch (e) {}
		})();
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="rf-skip screen-reader-text" href="#rf-main" style="position:absolute;left:-9999px;">Zum Inhalt springen</a>

<header class="rf-header" id="rf-header">
	<div class="rf-container rf-header__bar">
		<?php echo rf_logo(); // phpcs:ignore ?>

		<nav class="rf-nav" aria-label="Hauptnavigation">
			<?php foreach ( rf_ia() as $cat ) :
				$has_mega = ! empty( $cat['items'] );
				if ( ! $has_mega ) : ?>
					<div class="rf-nav__item">
						<a class="rf-nav__link" href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><?php echo esc_html( $cat['nav'] ); ?></a>
					</div>
				<?php else : ?>
					<div class="rf-nav__item" data-mega>
						<button class="rf-nav__link" type="button" aria-expanded="false" aria-haspopup="true">
							<?php echo esc_html( $cat['nav'] ); ?><?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
						</button>
						<div class="rf-mega" role="menu">
							<div class="rf-mega__head">
								<span class="rf-mega__ico"><?php echo rf_icon( $cat['icon'] ); // phpcs:ignore ?></span>
								<div>
									<div class="rf-mega__title"><?php echo esc_html( $cat['label'] ); ?></div>
									<div class="rf-mega__tag"><?php echo esc_html( $cat['tagline'] ); ?></div>
								</div>
							</div>
							<div class="rf-mega__grid">
								<?php foreach ( $cat['items'] as $item ) : ?>
									<a class="rf-mega__link" role="menuitem" href="<?php echo esc_url( rf_item_url( $item, $cat ) ); ?>">
										<strong>
											<?php echo esc_html( $item['label'] ); ?>
											<?php if ( ! empty( $item['ready'] ) ) : ?><span class="rf-mega__badge">live</span><?php endif; ?>
										</strong>
										<span><?php echo esc_html( $item['desc'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
							<a class="rf-mega__all" href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>">
								Alle <?php echo esc_html( $cat['label'] ); ?> ansehen <?php echo rf_icon( 'arrow' ); // phpcs:ignore ?>
							</a>
						</div>
					</div>
				<?php endif;
			endforeach; ?>
		</nav>

		<div class="rf-head-actions">
			<button class="rf-iconbtn rf-theme-toggle" type="button" aria-label="Farbschema wechseln" title="Hell / Dunkel">
				<?php echo rf_icon( 'sun', 'rf-icon--sun' ); // phpcs:ignore ?>
				<?php echo rf_icon( 'moon', 'rf-icon--moon' ); // phpcs:ignore ?>
			</button>
			<a class="rf-btn rf-btn--primary rf-cta-doc" href="<?php echo esc_url( rf_url( 'kuendigungsschreiben' ) ); ?>">Dokument starten</a>
			<button class="rf-iconbtn rf-burger" type="button" aria-label="Menü öffnen" aria-expanded="false">
				<?php echo rf_icon( 'menu' ); // phpcs:ignore ?>
			</button>
		</div>
	</div>
</header>

<!-- Mobile-Panel -->
<div class="rf-mobile" id="rf-mobile" aria-hidden="true">
	<div class="rf-mobile__top">
		<?php echo rf_logo(); // phpcs:ignore ?>
		<button class="rf-iconbtn rf-mobile__close" type="button" aria-label="Menü schließen">
			<?php echo rf_icon( 'close' ); // phpcs:ignore ?>
		</button>
	</div>
	<nav class="rf-mobile__nav" aria-label="Mobile Navigation">
		<?php foreach ( rf_ia() as $cat ) :
			$has_items = ! empty( $cat['items'] );
			if ( ! $has_items ) : ?>
				<div class="rf-acc">
					<a class="rf-acc__btn" href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><?php echo esc_html( $cat['label'] ); ?></a>
				</div>
			<?php else : ?>
				<div class="rf-acc">
					<button class="rf-acc__btn" type="button" aria-expanded="false">
						<?php echo esc_html( $cat['label'] ); ?><?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
					</button>
					<div class="rf-acc__panel">
						<a class="rf-acc__link" href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><strong>Übersicht</strong></a>
						<?php foreach ( $cat['items'] as $item ) : ?>
							<a class="rf-acc__link" href="<?php echo esc_url( rf_item_url( $item, $cat ) ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif;
		endforeach; ?>
	</nav>
	<div class="rf-mobile__cta">
		<button class="rf-iconbtn rf-theme-toggle rf-theme-toggle--mobile" type="button" aria-label="Farbschema wechseln" style="width:100%;gap:.5rem;">
			<?php echo rf_icon( 'sun', 'rf-icon--sun' ); // phpcs:ignore ?>
			<?php echo rf_icon( 'moon', 'rf-icon--moon' ); // phpcs:ignore ?>
			<span style="font-weight:600;">Hell / Dunkel</span>
		</button>
		<a class="rf-btn rf-btn--primary rf-btn--block" style="margin-top:.75rem;" href="<?php echo esc_url( rf_url( 'kuendigungsschreiben' ) ); ?>">Dokument starten</a>
	</div>
</div>

<main id="rf-main">
