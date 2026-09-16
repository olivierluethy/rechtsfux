/* Rechtsfux — Live-Dokumentvorschau */
(function () {
	'use strict';
	var doc = document.querySelector('.rf-doc');
	if (!doc) return;

	var form = doc.querySelector('.rf-form');
	var preview = doc.querySelector('[data-preview]');

	/* Paywall-Schutz für die Live-Vorschau -------------------------------------
	 * Der Inhalt der Vorschau soll nicht per Maus herauskopierbar sein. Das
	 * Markieren unterbindet bereits das CSS (user-select: none); hier fangen wir
	 * zusätzlich das Rechtsklick-Kontextmenü ab, damit «Kopieren» nicht darüber
	 * zurückkommt. Der PDF-Weg (.rf-pdf) läuft separat und bleibt erlaubt. */
	preview.addEventListener('contextmenu', function (e) { e.preventDefault(); });

	/* Ist DIESES Dokument kostenpflichtig gesperrt? Eine spätere Paywall muss
	 * dafür nur `data-locked` am `.rf-doc`-Element setzen. Heute ist nichts
	 * gesperrt → Verhalten unverändert. */
	function isLocked() { return doc.hasAttribute('data-locked'); }

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

	/* PDF-Vorschau (A4) als echtes PDF.
	 * pdfmake erzeugt aus der Live-Vorschau ein vektorscharfes A4-PDF (Text
	 * auswählbar); der lokal gehostete PDF.js-Viewer zeigt es im Overlay an.
	 * Dadurch IST die Vorschau das PDF — Download und Vorschau sind identisch,
	 * und Zoom/Suche/Seiten-Navigation bringt der Viewer eingebaut mit. */
	var pdfBtn = doc.querySelector('.rf-pdf');
	if (pdfBtn) {
		var overlay = null;
		var currentPdf = null;   // aktuelles pdfmake-Objekt (für «Speichern»)
		var blobUrl = null;      // Blob-URL im Viewer (zum Freigeben)

		function svg(path) {
			return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
				'stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + path + '</svg>';
		}
		var icoDownload = '<path d="M12 4v10"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/>';

		function docName() {
			var h = document.querySelector('.rf-pagehead h1, .rf-pagehead__title, h1');
			return h ? h.textContent.trim() : 'Rechtsfux-Dokument';
		}

		/* ---- DOM → pdfmake: ein generischer Übersetzer ----------------------
		 * Die 8 Dokumente nutzen dasselbe Klassen-Vokabular (.doc-title,
		 * .doc-sender, .doc-recipient, .doc-place, .doc-subject, .doc-body p,
		 * .doc-clause, .doc-sign .line) plus <strong>/<em>. Der Übersetzer liest
		 * die gefüllte .rf-preview-DOM — es gibt also nur EINE Layout-Quelle
		 * (das PHP-Template). Neue Dokumente funktionieren automatisch. */

		// Inline-Formatierung eines Elements → Array von pdfmake-Textstücken.
		function inlineNodes(node, inherited) {
			var out = [];
			Array.prototype.forEach.call(node.childNodes, function (child) {
				if (child.nodeType === 3) { // Text
					out.push({ text: child.nodeValue.replace(/\s+/g, ' '), bold: inherited.bold, italics: inherited.italics });
				} else if (child.nodeType === 1) { // Element
					var tag = child.tagName;
					if (tag === 'BR') {
						out.push({ text: '\n' });
					} else if (tag === 'STRONG' || tag === 'B') {
						out = out.concat(inlineNodes(child, { bold: true, italics: inherited.italics }));
					} else if (tag === 'EM' || tag === 'I') {
						out = out.concat(inlineNodes(child, { bold: inherited.bold, italics: true }));
					} else { // MARK, SPAN, … — Formatierung erben
						out = out.concat(inlineNodes(child, inherited));
					}
				}
			});
			return out;
		}

		// Whitespace um Zeilenumbrüche/Ränder bereinigen (wie HTML es rendert).
		function normalize(items) {
			var res = [], atStart = true;
			items.forEach(function (it) {
				if (it.text === '\n') {
					if (res.length) { var p = res[res.length - 1]; p.text = p.text.replace(/\s+$/, ''); if (p.text === '') res.pop(); }
					res.push({ text: '\n' });
					atStart = true;
					return;
				}
				var t = it.text.replace(/\s+/g, ' ');
				if (atStart) t = t.replace(/^\s+/, '');
				if (t === '') return;
				res.push({ text: t, bold: it.bold, italics: it.italics });
				atStart = false;
			});
			while (res.length) {
				var l = res[res.length - 1];
				if (l.text === '\n') { res.pop(); continue; }
				l.text = l.text.replace(/\s+$/, '');
				if (l.text === '') { res.pop(); continue; }
				break;
			}
			return res.length ? res : [{ text: '' }];
		}

		function inline(el) { return normalize(inlineNodes(el, {})); }
		function plain(el) { return (el.textContent || '').replace(/\s+/g, ' ').trim(); }

		// Unterschriftszeile: dünne Linie + kleine Beschriftung darunter.
		// Bewusst als Tabelle mit oberem Rahmen statt als `canvas` umgesetzt:
		// pdfmakes Browser-Build berechnet die Höhe eines canvas-Elements falsch
		// und erzwingt dadurch einen vorzeitigen Seitenumbruch vor der Signatur.
		// Fliess-Inhalt (Tabelle) wird korrekt gemessen → Dokument bleibt einseitig.
		function signatureBlock(label) {
			return {
				width: 'auto',
				table: { widths: [200], body: [ [ { text: label, fontSize: 9, color: '#55625b', border: [false, true, false, false] } ] ] },
				layout: {
					hLineWidth: function () { return 0.75; },
					hLineColor: function () { return '#b9c2bb'; },
					vLineWidth: function () { return 0; },
					paddingLeft: function () { return 0; },
					paddingRight: function () { return 0; },
					paddingTop: function () { return 3; },
					paddingBottom: function () { return 0; }
				}
			};
		}

		function buildDocDefinition(previewEl) {
			var content = [];
			Array.prototype.forEach.call(previewEl.children, function (el) {
				var c = el.classList;
				if (c.contains('doc-title')) {
					content.push({ text: plain(el), style: 'docTitle' });
				} else if (c.contains('doc-sender') || c.contains('doc-recipient')) {
					content.push({ text: inline(el), margin: [0, 0, 0, 14] });
				} else if (c.contains('doc-place')) {
					var left = (el.style.textAlign === 'left');
					content.push({ text: inline(el), alignment: left ? 'left' : 'right', color: '#55625b', margin: [0, left ? 12 : 0, 0, left ? 14 : 20] });
				} else if (c.contains('doc-subject')) {
					content.push({ text: inline(el), bold: true, margin: [0, 0, 0, 16] });
				} else if (c.contains('doc-body')) {
					Array.prototype.forEach.call(el.children, function (p) {
						content.push({ text: inline(p), margin: [0, 0, 0, 10] });
					});
				} else if (c.contains('doc-clause')) {
					var strong = el.querySelector('strong');
					var title = strong ? plain(strong) : '';
					var clone = el.cloneNode(true);
					var s = clone.querySelector('strong'); if (s) s.parentNode.removeChild(s);
					content.push({ stack: [ { text: title, bold: true, margin: [0, 0, 0, 2] }, { text: inline(clone) } ], margin: [0, 0, 0, 12] });
				} else if (c.contains('doc-sign')) {
					var lines = el.querySelectorAll('.line');
					if (lines.length > 1) {
						var cols = [];
						Array.prototype.forEach.call(lines, function (ln) { cols.push(signatureBlock(plain(ln))); });
						content.push({ columns: cols, columnGap: 24, margin: [0, 36, 0, 0] });
					} else if (lines.length === 1) {
						var sig = signatureBlock(plain(lines[0]));
						sig.margin = [0, 36, 0, 0];
						content.push(sig);
					}
				} else if (el.tagName === 'P') {
					content.push({ text: inline(el), margin: [0, 0, 0, 10] });
				} else {
					var t = inline(el);
					if (t.length && (t.length > 1 || t[0].text !== '')) content.push({ text: t, margin: [0, 0, 0, 10] });
				}
			});

			return {
				pageSize: 'A4',
				pageMargins: [72, 72, 72, 72], // 2.5 cm
				info: { title: docName() },
				defaultStyle: { fontSize: 11, lineHeight: 1.35, color: '#16211c' },
				styles: { docTitle: { fontSize: 17, bold: true, alignment: 'center', margin: [0, 0, 0, 18] } },
				content: content
			};
		}

		/* ---- Overlay mit eingebettetem PDF.js-Viewer ----------------------- */
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
							'<button type="button" class="rf-btn rf-btn--soft" data-close>Schliessen</button>' +
						'</div>' +
					'</div>' +
					'<div class="rf-pdf-body"><iframe class="rf-pdf-frame" title="PDF-Vorschau"></iframe></div>' +
				'</div>';
			document.body.appendChild(overlay);

			overlay.addEventListener('click', function (e) {
				if (e.target.closest('[data-close]')) close();
			});
			overlay.querySelector('.rf-pdf-save').addEventListener('click', function () {
				if (currentPdf) currentPdf.download(docName() + '.pdf');
			});
		}

		function open() {
			if (!window.pdfMake) { window.alert('PDF-Bibliothek konnte nicht geladen werden. Bitte laden Sie die Seite neu.'); return; }
			if (!overlay) build();
			update(); // sicherstellen, dass die Vorschau aktuell ist
			currentPdf = pdfMake.createPdf(buildDocDefinition(preview));

			var frame = overlay.querySelector('.rf-pdf-frame');
			var viewer = (window.RF_DOC && RF_DOC.pdfjsViewer) ? RF_DOC.pdfjsViewer : '';
			currentPdf.getBlob(function (blob) {
				if (blobUrl) URL.revokeObjectURL(blobUrl);
				blobUrl = URL.createObjectURL(blob);
				frame.src = viewer + '?file=' + encodeURIComponent(blobUrl) + '#zoom=page-width';
			});

			overlay.hidden = false;
			document.body.classList.add('is-pdf-open');
		}
		function close() {
			if (overlay) {
				overlay.hidden = true;
				var frame = overlay.querySelector('.rf-pdf-frame');
				if (frame) frame.src = 'about:blank';
			}
			if (blobUrl) { URL.revokeObjectURL(blobUrl); blobUrl = null; }
			document.body.classList.remove('is-pdf-open');
		}

		pdfBtn.addEventListener('click', open);
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay && !overlay.hidden) close();
		});
	}

	/* Kopieren */
	var copyBtn = doc.querySelector('.rf-copy');
	if (copyBtn && isLocked()) {
		// Kostenpflichtig gesperrtes Dokument: Kopieren-Button entfernen,
		// damit die Vorschau nicht per Klick herauskopiert werden kann.
		copyBtn.parentNode && copyBtn.parentNode.removeChild(copyBtn);
		copyBtn = null;
	}
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
