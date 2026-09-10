/* Rechtsfux — Live-Dokumentvorschau */
(function () {
	'use strict';
	var doc = document.querySelector('.rf-doc');
	if (!doc) return;

	var form = doc.querySelector('.rf-form');
	var preview = doc.querySelector('[data-preview]');

	var months = ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
	function formatDate(val) {
		// val im Format YYYY-MM-DD
		var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(val);
		if (!m) return val;
		return parseInt(m[3], 10) + '. ' + months[parseInt(m[2], 10) - 1] + ' ' + m[1];
	}

	function update() {
		var binds = preview.querySelectorAll('[data-bind]');
		Array.prototype.forEach.call(binds, function (el) {
			var key = el.getAttribute('data-bind');
			if (key === '__today') return;
			var field = form.querySelector('[name="' + key + '"]');
			if (!field) return;
			var val = (field.value || '').trim();
			var filled = !!val;
			if (filled && el.hasAttribute('data-date')) {
				val = formatDate(val);
			}
			el.textContent = filled ? val : (el.getAttribute('data-empty') || '');
			el.classList.toggle('is-filled', filled);
			// Bei ausgefülltem Wert die Markierung entfernen (kein gelber Hintergrund).
			el.style.background = filled ? 'transparent' : '';
			el.style.color = filled ? 'inherit' : '';
		});
	}

	form.addEventListener('input', update);
	form.addEventListener('change', update);
	update();

	/* PDF-Vorschau (A4) + «Als PDF speichern» über die Browser-Druckfunktion.
	 * Das Overlay zeigt das Dokument als echte A4-Seite (WYSIWYG) — man sieht,
	 * wie das PDF aussieht, ohne es herunterladen zu müssen. */
	var pdfBtn = doc.querySelector('.rf-pdf');
	if (pdfBtn) {
		var overlay = null;

		function svg(path) {
			return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
				'stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + path + '</svg>';
		}
		var icoDownload = '<path d="M12 4v10"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/>';
		var icoPrint = '<path d="M7 8V3h10v5"/><rect x="4" y="8" width="16" height="8" rx="1.5"/><path d="M7 14h10v6H7z"/>';

		function docName() {
			var h = document.querySelector('.rf-pagehead h1, .rf-pagehead__title, h1');
			return h ? h.textContent.trim() : 'Rechtsfux-Dokument';
		}

		function build() {
			overlay = document.createElement('div');
			overlay.className = 'rf-pdf-modal';
			overlay.hidden = true;
			overlay.innerHTML =
				'<div class="rf-pdf-modal__backdrop" data-close></div>' +
				'<div class="rf-pdf-modal__panel" role="dialog" aria-modal="true" aria-label="PDF-Vorschau">' +
					'<div class="rf-pdf-modal__bar">' +
						'<span class="rf-pdf-modal__title">Vorschau — so sieht Ihr PDF aus</span>' +
						'<div class="rf-pdf-modal__actions">' +
							'<button type="button" class="rf-btn rf-btn--primary rf-pdf-save">' + svg(icoDownload) + ' Als PDF speichern</button>' +
							'<button type="button" class="rf-btn rf-btn--ghost rf-pdf-print">' + svg(icoPrint) + ' Drucken</button>' +
							'<button type="button" class="rf-btn rf-btn--soft" data-close>Schliessen</button>' +
						'</div>' +
					'</div>' +
					'<div class="rf-pdf-scroll"><div class="rf-a4"><div class="rf-preview" data-a4-doc></div></div></div>' +
				'</div>';
			document.body.appendChild(overlay);

			overlay.addEventListener('click', function (e) {
				if (e.target.closest('[data-close]')) close();
			});
			overlay.querySelector('.rf-pdf-save').addEventListener('click', printDoc);
			overlay.querySelector('.rf-pdf-print').addEventListener('click', printDoc);
		}

		function open() {
			if (!overlay) build();
			// Aktuellen Vorschauinhalt in die A4-Seite spiegeln.
			overlay.querySelector('[data-a4-doc]').innerHTML = preview.innerHTML;
			overlay.hidden = false;
			document.body.classList.add('is-pdf-open');
		}
		function close() {
			if (overlay) overlay.hidden = true;
			document.body.classList.remove('is-pdf-open');
		}
		function printDoc() {
			// Tab-Titel kurz auf den Dokumentnamen setzen → schöner PDF-Dateiname.
			var prev = document.title;
			document.title = docName();
			window.print();
			setTimeout(function () { document.title = prev; }, 600);
		}

		pdfBtn.addEventListener('click', open);
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay && !overlay.hidden) close();
		});
	}

	/* Kopieren */
	var copyBtn = doc.querySelector('.rf-copy');
	if (copyBtn) {
		copyBtn.addEventListener('click', function () {
			var text = preview.innerText.replace(/\n{3,}/g, '\n\n').trim();
			var done = function () {
				var orig = copyBtn.innerHTML;
				copyBtn.innerHTML = '✓ Kopiert';
				setTimeout(function () { copyBtn.innerHTML = orig; }, 1600);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(done, done);
			} else {
				var ta = document.createElement('textarea');
				ta.value = text; document.body.appendChild(ta); ta.select();
				try { document.execCommand('copy'); } catch (e) {}
				document.body.removeChild(ta); done();
			}
		});
	}

	/* Kündigungsfrist nach OR (Art. 335c) — nur beim Arbeits-Kündigungsbrief */
	if (doc.getAttribute('data-doc') === 'kuendigung-arbeit') {
		var eintritt = form.querySelector('[name="eintritt"]');
		var termin = form.querySelector('[name="termin"]');
		var info = doc.querySelector('[data-frist-info]');
		var terminTouched = false;

		termin.addEventListener('input', function (e) { if (e.isTrusted) terminTouched = true; });

		function lastDayOfMonth(y, mZeroBased) { return new Date(y, mZeroBased + 1, 0); }
		function toISO(d) {
			return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
		}
		function computeFrist() {
			if (!eintritt.value) { info.textContent = ''; return; }
			var start = new Date(eintritt.value);
			if (isNaN(start.getTime())) { info.textContent = ''; return; }
			var today = new Date();
			var completedYears = (today - start) / (365.25 * 24 * 3600 * 1000);
			// OR 335c: 1. Jahr = 1 Monat, 2.–9. Jahr = 2 Monate, ab 10. Jahr = 3 Monate; jeweils auf Monatsende.
			var frist = completedYears < 1 ? 1 : (completedYears < 9 ? 2 : 3);
			var earliest = lastDayOfMonth(today.getFullYear(), today.getMonth() + frist);
			info.textContent = 'Kündigungsfrist nach OR: ' + frist + ' Monat' + (frist > 1 ? 'e' : '') +
				' auf Monatsende → frühestmöglicher Beendigungstermin: ' + earliest.toLocaleDateString('de-CH') + '.';
			if (!terminTouched) {
				termin.value = toISO(earliest);
				termin.dispatchEvent(new Event('input', { bubbles: true }));
			}
		}
		eintritt.addEventListener('input', computeFrist);
	}
})();
