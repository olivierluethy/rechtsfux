/* Rechtsfux — Rechner */
(function () {
	'use strict';

	function fmt(n) {
		return new Intl.NumberFormat('de-CH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
	}
	function fmtChf(n) { return 'CHF ' + fmt(n); }
	function row(label, val, big) {
		if (big) return '<div class="rf-result__big">' + val + '</div>';
		return '<div class="rf-result__row"><span>' + label + '</span><span>' + val + '</span></div>';
	}
	function el(id) { return document.getElementById(id); }

	/* ---------------- Segmented control helper ---------------- */
	function seg(id, onChange) {
		var wrap = el(id);
		if (!wrap) return function () { return null; };
		var current = wrap.querySelector('.is-active');
		wrap.addEventListener('click', function (e) {
			var b = e.target.closest('button');
			if (!b) return;
			wrap.querySelectorAll('button').forEach(function (x) { x.classList.remove('is-active'); });
			b.classList.add('is-active');
			onChange();
		});
		return function () {
			var a = wrap.querySelector('.is-active');
			return a ? a.getAttribute('data-v') : null;
		};
	}

	/* ===================== ERBSCHAFT ===================== */
	(function () {
		if (!el('erbschaft')) return;
		var wert = el('erb_wert'), fall = el('erb_fall'), kinder = el('erb_kinder'), out = el('erb_out');
		var kinderWrap = el('erb_kinderwrap');

		// Anteile [gesetzlich, pflichtteil] als Brüche des Nachlasses.
		var config = {
			ehe_kinder: [{ n: 'Ehepartner/in', g: 1 / 2, p: 1 / 4 }, { n: 'Kinder (zusammen)', g: 1 / 2, p: 1 / 4, kids: true }],
			kinder:     [{ n: 'Kinder (zusammen)', g: 1, p: 1 / 2, kids: true }],
			ehe_eltern: [{ n: 'Ehepartner/in', g: 3 / 4, p: 3 / 8 }, { n: 'Eltern (zusammen)', g: 1 / 4, p: 0 }],
			ehe:        [{ n: 'Ehepartner/in', g: 1, p: 1 / 2 }],
			eltern:     [{ n: 'Eltern (zusammen)', g: 1, p: 0 }]
		};

		function render() {
			var v = Math.max(0, parseFloat(wert.value) || 0);
			var f = fall.value;
			var groups = config[f];
			var showKids = groups.some(function (g) { return g.kids; });
			kinderWrap.style.display = showKids ? '' : 'none';
			var nKids = Math.max(1, parseInt(kinder.value, 10) || 1);

			var html = row(null, fmtChf(v), true) + '<div style="color:var(--ink-3);font-size:.82rem;margin:-.2rem 0 .4rem;">Nachlass gesamt</div>';
			var pflichtSum = 0;
			groups.forEach(function (g) {
				var ge = v * g.g, pf = v * g.p;
				pflichtSum += pf;
				html += row(g.n + ' — gesetzlich', fmtChf(ge));
				if (g.p > 0) html += row(g.n + ' — Pflichtteil', fmtChf(pf));
				if (g.kids && nKids > 1) html += row('→ je Kind (gesetzlich)', fmtChf(ge / nKids));
			});
			html += row('Freie Quote (frei verfügbar)', fmtChf(v - pflichtSum));
			out.innerHTML = html;
		}
		[wert, fall, kinder].forEach(function (n) { n.addEventListener('input', render); n.addEventListener('change', render); });
		render();
	})();

	/* ===================== BUSSE ===================== */
	(function () {
		if (!el('busse')) return;
		var kmh = el('busse_kmh'), out = el('busse_out');
		// Ordnungsbussen-Staffelung (netto). Schwelle = ab hier Anzeige.
		var tables = {
			innerorts: [{ max: 5, b: 40 }, { max: 10, b: 120 }, { max: 15, b: 250 }],
			ausserorts: [{ max: 5, b: 40 }, { max: 10, b: 100 }, { max: 15, b: 160 }, { max: 20, b: 240 }],
			autobahn: [{ max: 5, b: 20 }, { max: 10, b: 60 }, { max: 15, b: 120 }, { max: 20, b: 180 }, { max: 25, b: 260 }]
		};
		var getOrt = seg('busse_ort', function () { render(); });

		function render() {
			var ort = getOrt() || 'innerorts';
			var k = Math.max(0, parseInt(kmh.value, 10) || 0);
			var t = tables[ort];
			var hit = null;
			for (var i = 0; i < t.length; i++) { if (k <= t[i].max) { hit = t[i]; break; } }
			var label = { innerorts: 'Innerorts', ausserorts: 'Ausserorts', autobahn: 'Autobahn' }[ort];
			if (hit) {
				out.innerHTML = row(null, fmtChf(hit.b), true) +
					'<div style="color:var(--ink-3);font-size:.82rem;margin:-.2rem 0 .4rem;">Ordnungsbusse</div>' +
					row('Bereich', label) +
					row('Überschreitung', k + ' km/h') +
					row('Verfahren', 'Ordnungsbusse');
			} else {
				var over = t[t.length - 1].max;
				out.innerHTML = '<div class="rf-result__big" style="color:var(--amber);font-size:1.6rem;">Anzeige</div>' +
					'<div style="color:var(--ink-3);font-size:.82rem;margin:-.2rem 0 .4rem;">keine Ordnungsbusse mehr</div>' +
					row('Bereich', label) +
					row('Überschreitung', k + ' km/h') +
					row('Ab', 'über ' + over + ' km/h') +
					row('Folge', 'Anzeige · evtl. Ausweisentzug');
			}
		}
		kmh.addEventListener('input', render);
		render();
	})();

	/* ===================== MWST ===================== */
	(function () {
		if (!el('mwst')) return;
		var betrag = el('mwst_betrag'), satz = el('mwst_satz'), out = el('mwst_out');
		var getDir = seg('mwst_dir', function () { render(); });

		function render() {
			var b = Math.max(0, parseFloat(betrag.value) || 0);
			var s = parseFloat(satz.value) / 100;
			var dir = getDir() || 'netto';
			var netto, mwst, brutto;
			if (dir === 'netto') { netto = b; mwst = b * s; brutto = b + mwst; }
			else { brutto = b; netto = b / (1 + s); mwst = brutto - netto; }
			out.innerHTML = row(null, fmtChf(brutto), true) +
				'<div style="color:var(--ink-3);font-size:.82rem;margin:-.2rem 0 .4rem;">Bruttobetrag (inkl. MwSt)</div>' +
				row('Netto (exkl.)', fmtChf(netto)) +
				row('MwSt (' + (s * 100).toFixed(1) + ' %)', fmtChf(mwst)) +
				row('Brutto (inkl.)', fmtChf(brutto));
		}
		betrag.addEventListener('input', render);
		satz.addEventListener('change', render);
		render();
	})();

	/* ===================== LOHN (13. Monatslohn) ===================== */
	(function () {
		if (!el('lohn')) return;
		var betrag = el('lohn_betrag'), monate = el('lohn_monate'), out = el('lohn_out');
		function render() {
			var m = Math.max(0, parseFloat(betrag.value) || 0);
			var months = Math.min(12, Math.max(0, parseInt(monate.value, 10) || 0));
			var anteilig = m * months / 12;
			out.innerHTML = row(null, fmtChf(anteilig), true) +
				'<div style="color:var(--ink-3);font-size:.82rem;margin:-.2rem 0 .4rem;">Anteiliger 13. Monatslohn</div>' +
				row('Voller 13. Monatslohn', fmtChf(m)) +
				row('Beschäftigung', months + ' von 12 Monaten') +
				row('Pro Monat zurückstellen', fmtChf(m / 12));
		}
		betrag.addEventListener('input', render);
		monate.addEventListener('input', render);
		render();
	})();
})();
