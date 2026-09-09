<?php
/**
 * Template Name: Tools
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
			<span>Tools</span>
		</nav>
		<div class="rf-pagehead__ico"><?php echo rf_icon( 'calculator' ); // phpcs:ignore ?></div>
		<h1>Rechner, die sofort rechnen</h1>
		<p class="rf-sub">Vier kleine Helfer für den Schweizer Alltag — ohne Anmeldung, direkt in Ihrem Browser. Werte gelten als Orientierung ohne Gewähr.</p>
	</div>
</section>

<section class="rf-section" style="padding-top:1rem;">
	<div class="rf-container rf-narrow">

		<!-- ============ ERBSCHAFT ============ -->
		<div class="rf-calc" id="erbschaft" data-calc="erbschaft">
			<div class="rf-calc__head">
				<span class="rf-calc__ico"><?php echo rf_icon( 'scale' ); // phpcs:ignore ?></span>
				<div><h3>Erbschaftsrechner</h3><p>Gesetzliche Erbteile &amp; Pflichtteile (Schweizer Erbrecht, ab 2023)</p></div>
			</div>
			<div class="rf-calc__grid">
				<div>
					<div class="rf-field"><label>Nachlasswert (CHF)</label><input class="rf-input" type="number" id="erb_wert" value="500000" min="0" step="1000"></div>
					<div class="rf-field"><label>Familiensituation</label>
						<select class="rf-select" id="erb_fall">
							<option value="ehe_kinder">Ehepartner/in und Kinder</option>
							<option value="kinder">Nur Kinder</option>
							<option value="ehe_eltern">Ehepartner/in und Eltern</option>
							<option value="ehe">Nur Ehepartner/in</option>
							<option value="eltern">Nur Eltern</option>
						</select>
					</div>
					<div class="rf-field" id="erb_kinderwrap"><label>Anzahl Kinder</label><input class="rf-input" type="number" id="erb_kinder" value="2" min="1" max="12"></div>
				</div>
				<div class="rf-result" id="erb_out"></div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'scale' ); // phpcs:ignore ?><span>Vereinfachte Darstellung nach ZGB. Die «freie Quote» können Sie per Testament frei zuweisen. Konkubinatspartner erben gesetzlich nicht.</span></div>
		</div>

		<!-- ============ BUSSE ============ -->
		<div class="rf-calc" id="busse" data-calc="busse">
			<div class="rf-calc__head">
				<span class="rf-calc__ico"><?php echo rf_icon( 'flag' ); // phpcs:ignore ?></span>
				<div><h3>Bussenrechner</h3><p>Geschwindigkeits­überschreitung (nach Toleranzabzug)</p></div>
			</div>
			<div class="rf-calc__grid">
				<div>
					<div class="rf-field"><label>Wo?</label>
						<div class="rf-seg" id="busse_ort" role="tablist">
							<button type="button" data-v="innerorts" class="is-active">Innerorts</button>
							<button type="button" data-v="ausserorts">Ausserorts</button>
							<button type="button" data-v="autobahn">Autobahn</button>
						</div>
					</div>
					<div class="rf-field"><label>Überschreitung (km/h) <span class="rf-help">bereinigt um Toleranz</span></label><input class="rf-input" type="number" id="busse_kmh" value="8" min="1" max="60"></div>
				</div>
				<div class="rf-result" id="busse_out"></div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'flag' ); // phpcs:ignore ?><span>Basiert auf der Ordnungsbussenliste. Über den angegebenen Schwellen erfolgt keine Ordnungsbusse mehr, sondern eine Anzeige — mit möglichem Führerausweisentzug.</span></div>
		</div>

		<!-- ============ MWST ============ -->
		<div class="rf-calc" id="mwst" data-calc="mwst">
			<div class="rf-calc__head">
				<span class="rf-calc__ico"><?php echo rf_icon( 'calculator' ); // phpcs:ignore ?></span>
				<div><h3>MwSt-Rechner</h3><p>Schweizer Mehrwertsteuer, brutto ↔ netto</p></div>
			</div>
			<div class="rf-calc__grid">
				<div>
					<div class="rf-field"><label>Betrag (CHF)</label><input class="rf-input" type="number" id="mwst_betrag" value="100" min="0" step="0.05"></div>
					<div class="rf-field"><label>Steuersatz</label>
						<select class="rf-select" id="mwst_satz">
							<option value="8.1">Normalsatz 8.1 %</option>
							<option value="2.6">Reduziert 2.6 %</option>
							<option value="3.8">Beherbergung 3.8 %</option>
						</select>
					</div>
					<div class="rf-field"><label>Der Betrag ist …</label>
						<div class="rf-seg" id="mwst_dir" role="tablist">
							<button type="button" data-v="netto" class="is-active">Netto (exkl.)</button>
							<button type="button" data-v="brutto">Brutto (inkl.)</button>
						</div>
					</div>
				</div>
				<div class="rf-result" id="mwst_out"></div>
			</div>
		</div>

		<!-- ============ LOHN (13. ML) ============ -->
		<div class="rf-calc" id="lohn" data-calc="lohn">
			<div class="rf-calc__head">
				<span class="rf-calc__ico"><?php echo rf_icon( 'contract' ); // phpcs:ignore ?></span>
				<div><h3>Lohnrechner: 13. Monatslohn</h3><p>Anteiliger 13. Monatslohn bei unterjährigem Ein- oder Austritt</p></div>
			</div>
			<div class="rf-calc__grid">
				<div>
					<div class="rf-field"><label>Bruttomonatslohn (CHF)</label><input class="rf-input" type="number" id="lohn_betrag" value="6500" min="0" step="50"></div>
					<div class="rf-field"><label>Beschäftigungsmonate im Jahr</label><input class="rf-input" type="number" id="lohn_monate" value="12" min="1" max="12"></div>
				</div>
				<div class="rf-result" id="lohn_out"></div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'clock' ); // phpcs:ignore ?><span>Ein 13. Monatslohn ist nur geschuldet, wenn er vereinbart ist. Bei unterjährigem Ein- oder Austritt wird er in der Regel anteilig (pro rata) ausbezahlt.</span></div>
		</div>

	</div>
</section>

<?php get_footer(); ?>
