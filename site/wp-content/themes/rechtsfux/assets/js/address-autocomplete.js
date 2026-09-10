/* Rechtsfux — Adress-Autovervollständigung über die offizielle Schweizer
 * Adresssuche (geo.admin.ch, api3). Kein API-Key, CORS-fähig.
 *
 * Zielfelder (in doc-forms.php ausgezeichnet):
 *   [data-rf-street]   — Strassenfeld; PLZ/Ort-Geschwister werden über den
 *                        Namenspräfix gefunden (abs_strasse → abs_plz/abs_ort)
 *                        und bei Auswahl automatisch gefüllt.
 *   [data-rf-locality] — reines Orts-/PLZ-Feld (brief_ort, ort, wohnort).
 *                        Wert "full" schreibt «PLZ Ort», sonst nur den Ort.
 */
(function () {
	'use strict';

	var API = 'https://api3.geo.admin.ch/rest/services/api/SearchServer';
	var MIN_CHARS = 3;
	var DEBOUNCE_MS = 200;

	/* Zerlegt ein geo.admin.ch-Label in Strasse, PLZ und Ort.
	 * Beispiel: "Bahnhofstrasse 1 <b>8001 Zürich</b>"
	 *        →  { strasse: "Bahnhofstrasse 1", plz: "8001", ort: "Zürich" }
	 * Der <b>-Block umschliesst bei Adressen stets «PLZ Ort». Fehlt er
	 * (z. B. reine Gemeinde), bleibt plz leer und ort trägt den Text. */
	function labelZuAdresse(label) {
		var strasse = '', plz = '', ort = '';
		var m = /<b>([\s\S]*?)<\/b>/i.exec(label);
		if (m) {
			strasse = stripTags(label.slice(0, m.index)).trim();
			var inner = stripTags(m[1]).trim();
			var p = /^(\d{4})\s+([\s\S]+)$/.exec(inner);
			if (p) { plz = p[1]; ort = p[2].trim(); }
			else { ort = inner; }
		} else {
			var plain = stripTags(label).trim();
			var p2 = /^(\d{4})\s+([\s\S]+)$/.exec(plain);
			if (p2) { plz = p2[1]; ort = p2[2].trim(); }
			else { ort = plain; }
		}
		return { strasse: strasse, plz: plz, ort: ort };
	}

	function stripTags(s) {
		return String(s == null ? '' : s).replace(/<[^>]*>/g, '');
	}
	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
		});
	}

	/* Einen einzelnen Controller an ein Feld hängen. */
	function attach(input, kind) {
		var field = input.closest('.rf-field') || input.parentNode;
		field.classList.add('rf-ac-host');

		var box = document.createElement('div');
		box.className = 'rf-ac';
		box.hidden = true;
		field.appendChild(box);

		// Dezenter Herkunftshinweis (Transparenz zum externen Aufruf).
		if (!field.querySelector('.rf-ac-note')) {
			var note = document.createElement('p');
			note.className = 'rf-ac-note';
			note.textContent = 'Adresssuche via geo.admin.ch';
			field.appendChild(note);
		}

		var timer = null;
		var controller = null;
		var items = [];
		var active = -1;
		var suppress = false; // nach einer Auswahl späte Antworten/Reopen unterdrücken

		function close() {
			clearTimeout(timer);
			if (controller) { controller.abort(); controller = null; }
			box.hidden = true; box.innerHTML = ''; items = []; active = -1;
		}

		function origins() {
			return kind === 'street' ? 'address' : 'zipcode,gg25';
		}

		function query(q) {
			if (controller) controller.abort();
			controller = window.AbortController ? new AbortController() : null;
			var url = API + '?type=locations&limit=8&origins=' + origins() +
				'&searchText=' + encodeURIComponent(q);
			fetch(url, controller ? { signal: controller.signal } : undefined)
				.then(function (r) { return r.ok ? r.json() : null; })
				.then(function (data) {
					if (suppress) return; // Auswahl erfolgte, während die Anfrage lief
					if (!data || !data.results) { close(); return; }
					items = data.results.map(function (r) {
						return labelZuAdresse(r.attrs && r.attrs.label ? r.attrs.label : '');
					}).filter(function (a) { return a.ort || a.strasse; });
					render(q);
				})
				.catch(function () { /* Netzfehler/Abbruch: still degradieren */ });
		}

		function render(q) {
			if (!items.length) { close(); return; }
			active = -1;
			box.hidden = false;
			box.innerHTML = items.map(function (a, i) {
				var main, sub;
				if (kind === 'street' && a.strasse) {
					main = a.strasse;
					sub = (a.plz ? a.plz + ' ' : '') + a.ort;
				} else {
					main = (a.plz ? a.plz + ' ' : '') + a.ort;
					sub = '';
				}
				return '<button type="button" class="rf-ac__item" data-i="' + i + '">' +
					'<span class="rf-ac__main">' + esc(main) + '</span>' +
					(sub ? '<span class="rf-ac__sub">' + esc(sub) + '</span>' : '') +
					'</button>';
			}).join('');
		}

		function choose(a) {
			if (!a) return;
			suppress = true; // bis zur nächsten echten Eingabe geschlossen halten
			if (kind === 'street') {
				input.value = a.strasse || input.value;
				fillSibling('plz', a.plz);
				fillSibling('ort', a.ort);
			} else if (input.getAttribute('data-rf-locality') === 'full') {
				setValue(input, (a.plz ? a.plz + ' ' : '') + a.ort);
			} else {
				setValue(input, a.ort || (a.plz ? a.plz + ' ' + a.ort : ''));
			}
			fireInput(input);
			close();
			input.focus();
		}

		/* PLZ/Ort-Geschwister über den Namenspräfix füllen (abs_strasse → abs_plz). */
		function fillSibling(suffix, value) {
			if (!value) return;
			var name = input.getAttribute('name') || '';
			var prefix = name.replace(/strasse$/, '');
			var form = input.form || input.closest('form');
			if (!form) return;
			var sib = form.querySelector('[name="' + prefix + suffix + '"]');
			if (sib) { setValue(sib, value); fireInput(sib); }
		}

		function setValue(el, v) { el.value = v; }
		function fireInput(el) { el.dispatchEvent(new Event('input', { bubbles: true })); }

		function setActive(next) {
			var btns = box.querySelectorAll('.rf-ac__item');
			if (!btns.length) return;
			active = (next + btns.length) % btns.length;
			Array.prototype.forEach.call(btns, function (b, i) {
				b.classList.toggle('is-active', i === active);
				if (i === active) b.scrollIntoView({ block: 'nearest' });
			});
		}

		input.addEventListener('input', function () {
			suppress = false; // echte Eingabe erlaubt Vorschläge wieder
			var q = input.value.trim();
			clearTimeout(timer);
			if (q.length < MIN_CHARS) { close(); return; }
			timer = setTimeout(function () { query(q); }, DEBOUNCE_MS);
		});

		input.addEventListener('keydown', function (e) {
			if (box.hidden) return;
			if (e.key === 'ArrowDown') { e.preventDefault(); setActive(active + 1); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(active - 1); }
			else if (e.key === 'Enter') {
				if (active > -1 && items[active]) { e.preventDefault(); choose(items[active]); }
			} else if (e.key === 'Escape') { close(); }
		});

		box.addEventListener('mousedown', function (e) {
			// mousedown statt click, damit das blur des Inputs die Auswahl nicht verschluckt.
			var btn = e.target.closest('.rf-ac__item');
			if (!btn) return;
			e.preventDefault();
			choose(items[parseInt(btn.getAttribute('data-i'), 10)]);
		});

		input.addEventListener('blur', function () { setTimeout(close, 120); });
	}

	var streets = document.querySelectorAll('[data-rf-street]');
	var localities = document.querySelectorAll('[data-rf-locality]');
	if (!streets.length && !localities.length) return;
	Array.prototype.forEach.call(streets, function (el) { attach(el, 'street'); });
	Array.prototype.forEach.call(localities, function (el) { attach(el, 'locality'); });
})();
