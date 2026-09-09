<?php
/**
 * Template Name: Startseite
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;

// Suchindex für die Befehlsleiste aufbauen.
$search = array();
foreach ( rf_ia() as $cat ) {
	$search[] = array( 'label' => $cat['label'], 'cat' => 'Kategorie', 'url' => rf_url( $cat['slug'] ) );
	foreach ( $cat['items'] as $item ) {
		$search[] = array( 'label' => $item['label'], 'cat' => $cat['nav'], 'url' => rf_item_url( $item, $cat ) );
	}
}

get_header();
?>

<!-- ===================== HERO ===================== -->
<section class="rf-hero">
	<div class="rf-hero__grid" aria-hidden="true"></div>
	<div class="rf-container">
		<div class="rf-hero__inner">
			<span class="rf-tick"><?php echo rf_icon( 'flag' ); // phpcs:ignore ?> Schweizer Recht, verständlich gemacht</span>
			<h1>Papierkram, der sich <span class="rf-hero__accent">selbst erledigt</span>.</h1>
			<p class="rf-hero__lead">Kündigungen, Verträge und Vorsorgedokumente — korrekt formuliert, fristgerecht, in unter zwei Minuten. Sie füllen aus, Rechtsfux formuliert. Alles bleibt in Ihrem Browser.</p>

			<div class="rf-command" data-command>
				<span class="rf-command__ico"><?php echo rf_icon( 'search' ); // phpcs:ignore ?></span>
				<label for="rf-cmd" class="screen-reader-text" style="position:absolute;left:-9999px;">Was möchten Sie erledigen?</label>
				<input id="rf-cmd" type="text" placeholder="Was möchten Sie erledigen? z.&nbsp;B. «Wohnung kündigen»" autocomplete="off">
				<div class="rf-command__results" hidden></div>
			</div>
			<div class="rf-command__hint">
				<span>Beliebt:</span>
				<a class="rf-chip" href="<?php echo esc_url( rf_url( 'wohnung-kuendigen' ) ); ?>">Wohnung kündigen</a>
				<a class="rf-chip" href="<?php echo esc_url( rf_url( 'arbeitsvertrag' ) ); ?>">Arbeitsvertrag</a>
				<a class="rf-chip" href="<?php echo esc_url( rf_url( 'patientenverfuegung' ) ); ?>">Patientenverfügung</a>
			</div>

			<div class="rf-trust">
				<span class="rf-trust__item"><?php echo rf_icon( 'clock' ); // phpcs:ignore ?> In &lt; 2 Minuten fertig</span>
				<span class="rf-trust__item"><?php echo rf_icon( 'scale' ); // phpcs:ignore ?> Auf Schweizer Recht abgestimmt</span>
				<span class="rf-trust__item"><?php echo rf_icon( 'lock' ); // phpcs:ignore ?> Daten bleiben im Browser</span>
			</div>
		</div>
	</div>
</section>

<div class="rf-container"><div class="rf-ruler" aria-hidden="true"></div></div>

<!-- ===================== PROZESS ===================== -->
<section class="rf-section rf-section--tight">
	<div class="rf-container rf-reveal">
		<h2>In drei Schritten zum fertigen Dokument</h2>
		<p class="rf-sub">Kein Login, keine versteckten Kosten, kein juristisches Fachchinesisch.</p>
		<div class="rf-steps">
			<div class="rf-step">
				<div class="rf-step__no">1</div>
				<h3>Dokument wählen</h3>
				<p>Suchen Sie oben, was Sie brauchen — oder stöbern Sie durch die Kategorien.</p>
			</div>
			<div class="rf-step">
				<div class="rf-step__no">2</div>
				<h3>Felder ausfüllen</h3>
				<p>Beantworten Sie ein paar einfache Fragen. Jedes Feld wird erklärt.</p>
			</div>
			<div class="rf-step">
				<div class="rf-step__no">3</div>
				<h3>Prüfen &amp; herunterladen</h3>
				<p>Sie sehen das fertige Dokument live, drucken es aus oder kopieren den Text.</p>
			</div>
		</div>
	</div>
</section>

<!-- ===================== KATEGORIEN ===================== -->
<section class="rf-section">
	<div class="rf-container">
		<div class="rf-reveal">
			<span class="rf-tick"><?php echo rf_icon( 'spark' ); // phpcs:ignore ?> Alles an einem Ort</span>
			<h2>Wobei dürfen wir helfen?</h2>
			<p class="rf-sub">Sechs Bereiche decken die häufigsten Anliegen im Schweizer Alltag ab.</p>
		</div>
		<div class="rf-cats">
			<?php foreach ( rf_ia() as $cat ) : ?>
				<article class="rf-cat rf-reveal">
					<div class="rf-cat__ico"><?php echo rf_icon( $cat['icon'] ); // phpcs:ignore ?></div>
					<h3><?php echo esc_html( $cat['label'] ); ?></h3>
					<p><?php echo esc_html( $cat['tagline'] ); ?></p>
					<span class="rf-cat__more">Ansehen <?php echo rf_icon( 'arrow' ); // phpcs:ignore ?></span>
					<a class="rf-cat__link" href="<?php echo esc_url( rf_url( $cat['slug'] ) ); ?>"><?php echo esc_html( $cat['label'] ); ?></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ===================== TOOLS-TEASER ===================== -->
<section class="rf-section rf-section--tight">
	<div class="rf-container">
		<div class="rf-split rf-reveal">
			<div class="rf-split__text">
				<span class="rf-tick"><?php echo rf_icon( 'calculator' ); // phpcs:ignore ?> Rechner</span>
				<h2>Zahlen, die sofort stimmen</h2>
				<p class="rf-sub">Vier kleine Helfer für grosse Fragen — ganz ohne Anmeldung, direkt im Browser gerechnet.</p>
				<a class="rf-btn rf-btn--primary" style="margin-top:1.25rem;" href="<?php echo esc_url( rf_url( 'tools' ) ); ?>">Zu den Tools <?php echo rf_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
			<div class="rf-split__media">
				<div class="rf-tool-preview">
					<a class="rf-tool-row" href="<?php echo esc_url( rf_url( 'tools#erbschaft' ) ); ?>">
						<span class="rf-tool-row__ico"><?php echo rf_icon( 'scale' ); // phpcs:ignore ?></span>
						<span><strong>Erbschaftsrechner</strong><span>Gesetzliche Erbteile &amp; Pflichtteile</span></span>
						<?php echo rf_icon( 'arrow' ); // phpcs:ignore ?>
					</a>
					<a class="rf-tool-row" href="<?php echo esc_url( rf_url( 'tools#busse' ) ); ?>">
						<span class="rf-tool-row__ico"><?php echo rf_icon( 'flag' ); // phpcs:ignore ?></span>
						<span><strong>Bussenrechner</strong><span>Tempo­überschreitung abschätzen</span></span>
						<?php echo rf_icon( 'arrow' ); // phpcs:ignore ?>
					</a>
					<a class="rf-tool-row" href="<?php echo esc_url( rf_url( 'tools#mwst' ) ); ?>">
						<span class="rf-tool-row__ico"><?php echo rf_icon( 'calculator' ); // phpcs:ignore ?></span>
						<span><strong>MwSt-Rechner</strong><span>Brutto ↔ Netto, Schweizer Sätze</span></span>
						<?php echo rf_icon( 'arrow' ); // phpcs:ignore ?>
					</a>
					<a class="rf-tool-row" href="<?php echo esc_url( rf_url( 'tools#lohn' ) ); ?>">
						<span class="rf-tool-row__ico"><?php echo rf_icon( 'contract' ); // phpcs:ignore ?></span>
						<span><strong>Lohnrechner</strong><span>Anteiliger 13. Monatslohn</span></span>
						<?php echo rf_icon( 'arrow' ); // phpcs:ignore ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ===================== RECHTSSCHUTZ ===================== -->
<section class="rf-section">
	<div class="rf-container">
		<div class="rf-split rf-split--rev rf-reveal">
			<div class="rf-split__media">
				<div class="rf-panel rf-panel--accent">
					<h3 style="margin-bottom:.4rem;">Wenn es doch ernst wird</h3>
					<p style="color:rgba(255,255,255,.85);margin-bottom:1.25rem;">Manche Streitigkeiten lassen sich nicht mit einem Brief lösen. Dann ist eine Rechtsschutz­versicherung Gold wert.</p>
					<ul class="rf-mini-list">
						<li style="color:rgba(255,255,255,.92);"><?php echo rf_icon( 'check' ); // phpcs:ignore ?> Beratung durch echte Juristinnen und Juristen</li>
						<li style="color:rgba(255,255,255,.92);"><?php echo rf_icon( 'check' ); // phpcs:ignore ?> Übernahme von Anwalts- und Gerichtskosten</li>
						<li style="color:rgba(255,255,255,.92);"><?php echo rf_icon( 'check' ); // phpcs:ignore ?> Für Arbeit, Wohnen, Konsum und Verkehr</li>
					</ul>
				</div>
			</div>
			<div class="rf-split__text">
				<span class="rf-tick"><?php echo rf_icon( 'shield' ); // phpcs:ignore ?> Im Ernstfall abgesichert</span>
				<h2>Selbst erledigen — und trotzdem geschützt</h2>
				<p class="rf-sub">Die meisten Anliegen lösen Sie mit Rechtsfux selbst. Für den Rest hilft ein starker Partner im Hintergrund. Wir zeigen Ihnen, worauf es bei einer Rechtsschutz­versicherung ankommt.</p>
				<a class="rf-btn rf-btn--ghost" style="margin-top:1.25rem;" href="<?php echo esc_url( rf_url( 'ratgeber' ) ); ?>">Mehr im Ratgeber</a>
			</div>
		</div>
	</div>
</section>

<!-- ===================== RATGEBER ===================== -->
<section class="rf-section rf-section--tight">
	<div class="rf-container">
		<div class="rf-reveal" style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
			<div>
				<span class="rf-tick"><?php echo rf_icon( 'compass' ); // phpcs:ignore ?> Ratgeber</span>
				<h2>Klug entscheiden mit dem Fux</h2>
			</div>
			<a class="rf-btn rf-btn--ghost" href="<?php echo esc_url( rf_url( 'ratgeber' ) ); ?>">Alle Beiträge</a>
		</div>
		<div class="rf-posts">
			<?php
			$q = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
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
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<!-- ===================== FAQ ===================== -->
<section class="rf-section">
	<div class="rf-container">
		<div class="rf-reveal">
			<h2>Häufige Fragen</h2>
			<p class="rf-sub">Das Wichtigste, bevor Sie loslegen.</p>
		</div>
		<div class="rf-faq rf-reveal">
			<?php
			$faqs = array(
				array( 'Sind die Dokumente rechtsgültig?', 'Unsere Vorlagen sind auf das Schweizer Recht abgestimmt und für den Normalfall gedacht. Bei aussergewöhnlichen Situationen empfehlen wir zusätzlich eine individuelle Rechtsberatung. Rechtsfux ersetzt keine anwaltliche Beratung im Einzelfall.' ),
				array( 'Was passiert mit meinen Daten?', 'Alles, was Sie in ein Formular eingeben, bleibt in Ihrem Browser. Es wird nichts an einen Server gesendet oder gespeichert. Schliessen Sie den Tab, sind die Daten weg.' ),
				array( 'Kostet Rechtsfux etwas?', 'Diese Demo ist kostenlos nutzbar. Sie füllen aus, sehen die Vorschau und drucken oder kopieren das Ergebnis.' ),
				array( 'Kann ich das Dokument nachträglich ändern?', 'Ja. Sie passen die Felder beliebig an — die Vorschau aktualisiert sich sofort. Erst wenn es stimmt, drucken Sie es aus.' ),
				array( 'Für wen ist Rechtsfux gedacht?', 'Für alle in der Schweiz, die alltägliche Rechtsthemen selbst und korrekt erledigen wollen — ohne teuren Umweg für Standardfälle.' ),
			);
			foreach ( $faqs as $i => $faq ) : ?>
				<div class="rf-faq__item<?php echo 0 === $i ? ' is-open' : ''; ?>">
					<button class="rf-faq__q" type="button" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<?php echo esc_html( $faq[0] ); ?><?php echo rf_icon( 'chevron' ); // phpcs:ignore ?>
					</button>
					<div class="rf-faq__a"><div class="rf-faq__a-inner"><?php echo esc_html( $faq[1] ); ?></div></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ===================== CTA ===================== -->
<section class="rf-section rf-section--tight">
	<div class="rf-container">
		<div class="rf-cta rf-reveal">
			<div class="rf-cta__grid" aria-hidden="true"></div>
			<h2>Erledigen Sie es jetzt — nicht «irgendwann»</h2>
			<p>Das Kündigungsschreiben, das seit Wochen wartet? In zwei Minuten erledigt.</p>
			<div class="rf-cta__actions">
				<a class="rf-btn rf-btn--primary rf-btn--lg" href="<?php echo esc_url( rf_url( 'wohnung-kuendigen' ) ); ?>">Kündigung erstellen</a>
				<a class="rf-btn rf-btn--ghost rf-btn--lg" href="<?php echo esc_url( rf_url( 'tools' ) ); ?>">Rechner ausprobieren</a>
			</div>
		</div>
	</div>
</section>

<script>window.RF_SEARCH = <?php echo wp_json_encode( $search ); ?>;</script>

<?php get_footer(); ?>
