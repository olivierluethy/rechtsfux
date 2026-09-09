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

	/* Drucken */
	var printBtn = doc.querySelector('.rf-print');
	if (printBtn) printBtn.addEventListener('click', function () { window.print(); });

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
				' auf Monatsende → frühestens per ' + earliest.toLocaleDateString('de-CH') + '.';
			if (!terminTouched) {
				termin.value = toISO(earliest);
				termin.dispatchEvent(new Event('input', { bubbles: true }));
			}
		}
		eintritt.addEventListener('input', computeFrist);
	}
})();
