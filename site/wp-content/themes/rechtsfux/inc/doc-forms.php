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
function rf_doc_arbeitsvertrag() { ?>
	<div class="rf-doc" data-doc="arbeitsvertrag">
		<form class="rf-form" autocomplete="off" onsubmit="return false;">
			<fieldset class="rf-fieldset">
				<legend>Vertragsparteien</legend>
				<div class="rf-field"><label>Arbeitgeber (Firma)</label><input class="rf-input" name="ag_name" placeholder="Muster AG, Zürich"></div>
				<div class="rf-field"><label>Arbeitnehmer/in</label><input class="rf-input" name="an_name" placeholder="Anna Muster"></div>
			</fieldset>
			<fieldset class="rf-fieldset">
				<legend>Anstellung</legend>
				<div class="rf-field"><label>Funktion / Position</label><input class="rf-input" name="funktion" placeholder="Sachbearbeiterin Finanzen"></div>
				<div class="rf-field-row">
					<div class="rf-field"><label>Eintrittsdatum</label><input class="rf-input" type="date" name="eintritt"></div>
					<div class="rf-field"><label>Arbeitsort</label><input class="rf-input" name="ort" placeholder="Zürich"></div>
				</div>
				<div class="rf-field-row">
					<div class="rf-field"><label>Pensum (%)</label><input class="rf-input" type="number" name="pensum" placeholder="100" min="1" max="100"></div>
					<div class="rf-field"><label>Probezeit</label>
						<select class="rf-select" name="probezeit">
							<option value="1 Monat">1 Monat</option>
							<option value="2 Monate" selected>2 Monate</option>
							<option value="3 Monate">3 Monate</option>
							<option value="keine Probezeit">keine Probezeit</option>
						</select>
					</div>
				</div>
				<div class="rf-field-row">
					<div class="rf-field"><label>Bruttolohn (CHF)</label><input class="rf-input" name="lohn" placeholder="6500"></div>
					<div class="rf-field"><label>pro</label>
						<select class="rf-select" name="lohn_takt">
							<option value="Monat (× 13)" selected>Monat (× 13)</option>
							<option value="Monat (× 12)">Monat (× 12)</option>
							<option value="Jahr">Jahr</option>
						</select>
					</div>
				</div>
				<div class="rf-field"><label>Ferien (Wochen/Jahr)</label><input class="rf-input" type="number" name="ferien" placeholder="5" min="4" max="8"></div>
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
				<h4 class="doc-title">Arbeitsvertrag</h4>
				<p>zwischen <strong><?php echo rf_bind( 'ag_name', 'Arbeitgeber' ); // phpcs:ignore ?></strong> (Arbeitgeber)</p>
				<p>und <strong><?php echo rf_bind( 'an_name', 'Arbeitnehmer/in' ); // phpcs:ignore ?></strong> (Arbeitnehmer/in)</p>
				<div class="doc-clause"><strong>1. Funktion</strong><?php echo rf_bind( 'funktion', 'Funktion' ); // phpcs:ignore ?></div>
				<div class="doc-clause"><strong>2. Beginn &amp; Arbeitsort</strong>Das Arbeitsverhältnis beginnt am <mark data-bind="eintritt" data-empty="[Datum]" data-date>[Datum]</mark>. Arbeitsort ist <?php echo rf_bind( 'ort', 'Ort' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>3. Pensum</strong>Das Beschäftigungspensum beträgt <?php echo rf_bind( 'pensum', '100' ); // phpcs:ignore ?> %.</div>
				<div class="doc-clause"><strong>4. Lohn</strong>Der Bruttolohn beträgt CHF <?php echo rf_bind( 'lohn', 'Betrag' ); // phpcs:ignore ?> pro <?php echo rf_bind( 'lohn_takt', 'Monat (× 13)' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>5. Probezeit</strong>Es gilt eine Probezeit von <?php echo rf_bind( 'probezeit', '2 Monate' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>6. Ferien</strong>Der Ferienanspruch beträgt <?php echo rf_bind( 'ferien', '5' ); // phpcs:ignore ?> Wochen pro Jahr.</div>
				<div class="doc-clause"><strong>7. Übriges</strong>Im Übrigen gelten die Bestimmungen des Schweizerischen Obligationenrechts (Art. 319 ff. OR).</div>
				<div class="doc-sign" style="display:flex;gap:2rem;">
					<div class="line" style="flex:1;">Arbeitgeber</div>
					<div class="line" style="flex:1;">Arbeitnehmer/in</div>
				</div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'scale' ); // phpcs:ignore ?><span>Diese Vorlage deckt den Normalfall ab. Für Kaderfunktionen, Konkurrenzverbote oder Bonusregelungen empfiehlt sich eine individuelle Prüfung.</span></div>
		</div>
	</div>
<?php }

/* ------------------------------------------------------------------ */
function rf_doc_patientenverfuegung() { ?>
	<div class="rf-doc" data-doc="patientenverfuegung">
		<form class="rf-form" autocomplete="off" onsubmit="return false;">
			<fieldset class="rf-fieldset">
				<legend>Verfügende Person</legend>
				<div class="rf-field"><label>Vor- und Nachname</label><input class="rf-input" name="name" placeholder="Anna Muster"></div>
				<div class="rf-field-row">
					<div class="rf-field"><label>Geburtsdatum</label><input class="rf-input" type="date" name="geburtsdatum"></div>
					<div class="rf-field"><label>Wohnort</label><input class="rf-input" name="wohnort" placeholder="8001 Zürich"></div>
				</div>
			</fieldset>
			<fieldset class="rf-fieldset">
				<legend>Vertretungsperson</legend>
				<div class="rf-field"><label>Name der Vertrauensperson</label><input class="rf-input" name="vertreter" placeholder="Peter Muster"></div>
				<div class="rf-field"><label>Erreichbar unter</label><input class="rf-input" name="vertreter_tel" placeholder="079 000 00 00"></div>
			</fieldset>
			<fieldset class="rf-fieldset">
				<legend>Meine Wünsche</legend>
				<div class="rf-field"><label>Lebenserhaltende Massnahmen</label>
					<select class="rf-select" name="lebenserhalt">
						<option value="auf lebenserhaltende Massnahmen verzichtet werden soll, wenn keine Aussicht auf ein selbstbestimmtes Leben besteht" selected>Verzicht, wenn keine Aussicht auf Besserung</option>
						<option value="alle medizinisch sinnvollen lebenserhaltenden Massnahmen ausgeschöpft werden sollen">Alle sinnvollen Massnahmen ausschöpfen</option>
					</select>
				</div>
				<div class="rf-field"><label>Schmerzlinderung</label>
					<select class="rf-select" name="schmerz">
						<option value="eine wirksame Schmerz- und Symptomlinderung erhalte, auch wenn dies mein Leben verkürzen könnte" selected>Wirksame Linderung, auch wenn lebensverkürzend</option>
						<option value="eine Schmerzlinderung nur erhalte, soweit sie mein Leben nicht verkürzt">Nur soweit nicht lebensverkürzend</option>
					</select>
				</div>
				<div class="rf-field"><label>Organspende</label>
					<select class="rf-select" name="organ">
						<option value="mich zur Organspende bereit erkläre" selected>Ich bin zur Organspende bereit</option>
						<option value="eine Organspende ablehne">Ich lehne eine Organspende ab</option>
					</select>
				</div>
				<div class="rf-field"><label>Ort/Datum</label><input class="rf-input" name="brief_ort" placeholder="Zürich"></div>
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
				<h4 class="doc-title">Patientenverfügung</h4>
				<p>Ich, <strong><?php echo rf_bind( 'name', 'Vor- und Nachname' ); // phpcs:ignore ?></strong>, geboren am <mark data-bind="geburtsdatum" data-empty="[Geburtsdatum]" data-date>[Geburtsdatum]</mark>, wohnhaft in <?php echo rf_bind( 'wohnort', 'Wohnort' ); // phpcs:ignore ?>, verfüge für den Fall, dass ich meinen Willen nicht mehr äussern kann, was folgt:</p>
				<div class="doc-clause"><strong>1. Lebenserhaltende Massnahmen</strong>Ich wünsche, dass <?php echo rf_bind( 'lebenserhalt', '…' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>2. Schmerzbehandlung</strong>Ich wünsche, dass ich <?php echo rf_bind( 'schmerz', '…' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>3. Organspende</strong>Ich halte fest, dass ich <?php echo rf_bind( 'organ', '…' ); // phpcs:ignore ?>.</div>
				<div class="doc-clause"><strong>4. Vertretung</strong>Als meine vertretungsberechtigte Person bestimme ich <strong><?php echo rf_bind( 'vertreter', 'Name' ); // phpcs:ignore ?></strong> (erreichbar unter <?php echo rf_bind( 'vertreter_tel', 'Telefon' ); // phpcs:ignore ?>).</div>
				<div class="doc-place" style="text-align:left;margin-top:1.5rem;"><?php echo rf_bind( 'brief_ort', 'Ort' ); // phpcs:ignore ?>, <mark data-bind="__today" data-today><?php echo esc_html( date_i18n( 'j. F Y' ) ); ?></mark></div>
				<div class="doc-sign"><div class="line"><?php echo rf_bind( 'name', 'Unterschrift' ); // phpcs:ignore ?></div></div>
			</div>
			<div class="rf-note"><?php echo rf_icon( 'shield' ); // phpcs:ignore ?><span>Damit sie verbindlich ist, muss die Patientenverfügung eigenhändig datiert und unterschrieben werden. Hinterlegen Sie eine Kopie bei Ihrer Vertretungsperson und Ihrem Hausarzt.</span></div>
		</div>
	</div>
<?php }
