<?php
/**
 * Dokument-Formulare + Live-Vorschauen (drei Beispiel-Generatoren).
 *
 * Bindung: jedes <mark data-bind="key"> in der Vorschau wird von document-preview.js
 * mit dem Wert von input/select/textarea[name="key"] gefüllt (oder data-empty-Platzhalter).
 *
 * @package Rechtsfux
 */
defined( 'ABSPATH' ) || exit;

/** Platzhalter-Bindung in der Vorschau. */
function rf_bind( $key, $empty ) {
	return '<mark data-bind="' . esc_attr( $key ) . '" data-empty="' . esc_attr( $empty ) . '">' . esc_html( $empty ) . '</mark>';
}

/** Rendert Formular + Vorschau für den gegebenen Dokumenttyp. */
function rf_render_document( $type ) {
	switch ( $type ) {
		case 'arbeitsvertrag':
			rf_doc_arbeitsvertrag();
			break;
		case 'patientenverfuegung':
			rf_doc_patientenverfuegung();
			break;
		case 'kuendigung-wohnung':
		default:
			rf_doc_kuendigung_wohnung();
			break;
	}
}

/* ------------------------------------------------------------------ */
function rf_doc_kuendigung_wohnung() { ?>
	<div class="rf-doc" data-doc="kuendigung-wohnung">
		<form class="rf-form" autocomplete="off" onsubmit="return false;">
			<fieldset class="rf-fieldset">
				<legend>Ihre Angaben</legend>
				<div class="rf-field-row">
					<div class="rf-field"><label>Vorname</label><input class="rf-input" name="abs_vorname" placeholder="Anna"></div>
					<div class="rf-field"><label>Nachname</label><input class="rf-input" name="abs_nachname" placeholder="Muster"></div>
				</div>
				<div class="rf-field"><label>Strasse &amp; Nr.</label><input class="rf-input" name="abs_strasse" placeholder="Bahnhofstrasse 1"></div>
				<div class="rf-field-row">
					<div class="rf-field"><label>PLZ</label><input class="rf-input" name="abs_plz" placeholder="8001"></div>
					<div class="rf-field"><label>Ort</label><input class="rf-input" name="abs_ort" placeholder="Zürich"></div>
				</div>
			</fieldset>

			<fieldset class="rf-fieldset">
				<legend>Empfänger (Vermieter / Verwaltung)</legend>
				<div class="rf-field"><label>Name / Firma</label><input class="rf-input" name="emp_name" placeholder="Immobilien Verwaltung AG"></div>
				<div class="rf-field"><label>Strasse &amp; Nr.</label><input class="rf-input" name="emp_strasse" placeholder="Verwaltungsweg 5"></div>
				<div class="rf-field-row">
					<div class="rf-field"><label>PLZ</label><input class="rf-input" name="emp_plz" placeholder="8004"></div>
					<div class="rf-field"><label>Ort</label><input class="rf-input" name="emp_ort" placeholder="Zürich"></div>
				</div>
			</fieldset>

			<fieldset class="rf-fieldset">
				<legend>Mietobjekt &amp; Termin</legend>
				<div class="rf-field"><label>Adresse der Wohnung <span class="rf-help">falls abweichend</span></label><input class="rf-input" name="obj_adresse" placeholder="Bahnhofstrasse 1, 4. OG links"></div>
				<div class="rf-field-row">
					<div class="rf-field"><label>Kündigung per <span class="rf-help">nächster Termin</span></label><input class="rf-input" type="date" name="termin"></div>
					<div class="rf-field"><label>Ort/Datum des Schreibens</label><input class="rf-input" name="brief_ort" placeholder="Zürich"></div>
				</div>
			</fieldset>
		</form>

		<div class="rf-preview-wrap">
			<div class="rf-preview-bar">
				<span class="rf-preview-bar__label"><?php echo rf_icon( 'pen' ); // phpcs:ignore ?> Live-Vorschau</span>
				<div class="rf-preview-actions">
					<button class="rf-btn rf-btn--ghost rf-print" type="button"><?php echo rf_icon( 'print' ); // phpcs:ignore ?> Drucken</button>
					<button class="rf-btn rf-btn--soft rf-copy" type="button"><?php echo rf_icon( 'copy' ); // phpcs:ignore ?> Kopieren</button>
				</div>
			</div>
			<div class="rf-preview" data-preview>
				<div class="doc-sender">
					<?php echo rf_bind( 'abs_vorname', 'Vorname' ); // phpcs:ignore ?> <?php echo rf_bind( 'abs_nachname', 'Nachname' ); // phpcs:ignore ?><br>
					<?php echo rf_bind( 'abs_strasse', 'Strasse Nr.' ); // phpcs:ignore ?><br>
					<?php echo rf_bind( 'abs_plz', 'PLZ' ); // phpcs:ignore ?> <?php echo rf_bind( 'abs_ort', 'Ort' ); // phpcs:ignore ?>
				</div>
				<div class="doc-recipient">
					<?php echo rf_bind( 'emp_name', 'Name der Verwaltung' ); // phpcs:ignore ?><br>
					<?php echo rf_bind( 'emp_strasse', 'Strasse Nr.' ); // phpcs:ignore ?><br>
					<?php echo rf_bind( 'emp_plz', 'PLZ' ); // phpcs:ignore ?> <?php echo rf_bind( 'emp_ort', 'Ort' ); // phpcs:ignore ?>
				</div>
				<div class="doc-place"><?php echo rf_bind( 'brief_ort', 'Ort' ); // phpcs:ignore ?>, <mark data-bind="__today" data-today><?php echo esc_html( date_i18n( 'j. F Y' ) ); ?></mark></div>
				<div class="doc-subject">Kündigung des Mietvertrags — <?php echo rf_bind( 'obj_adresse', 'Adresse der Wohnung' ); // phpcs:ignore ?></div>
				<div class="doc-body">
					<p>Sehr geehrte Damen und Herren</p>
					<p>Hiermit kündige ich den Mietvertrag für die oben genannte Wohnung ordentlich und fristgerecht per <strong><mark data-bind="termin" data-empty="[Kündigungsdatum]" data-date>[Kündigungsdatum]</mark></strong>.</p>
					<p>Ich bitte Sie, mir den Empfang dieser Kündigung sowie den genauen Auszugstermin schriftlich zu bestätigen. Gerne vereinbare ich mit Ihnen einen Termin für die Wohnungsübergabe und das Übergabeprotokoll.</p>
					<p>Freundliche Grüsse</p>
				</div>
				<div class="doc-sign">
					<div class="line"><?php echo rf_bind( 'abs_vorname', 'Vorname' ); // phpcs:ignore ?> <?php echo rf_bind( 'abs_nachname', 'Nachname' ); // phpcs:ignore ?></div>
				</div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'clock' ); // phpcs:ignore ?><span>Beachten Sie Ihre Kündigungsfrist (meist 3 Monate auf einen ortsüblichen Termin). Senden Sie die Kündigung eingeschrieben — bei gemeinsamen Mietverträgen müssen alle Personen unterschreiben.</span></div>
		</div>
	</div>
<?php }

/* ------------------------------------------------------------------ */
