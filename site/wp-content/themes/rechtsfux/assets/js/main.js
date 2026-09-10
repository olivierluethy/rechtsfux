/* Rechtsfux — UI-Interaktionen */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;

	/* ---------- Theme-Umschalter ---------- */
	function currentTheme() {
		var attr = root.getAttribute('data-theme');
		if (attr) return attr;
		return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
	}
	function setTheme(t) {
		root.setAttribute('data-theme', t);
		try { localStorage.setItem('rf-theme', t); } catch (e) {}
		doc.querySelector('meta[name="theme-color"]') &&
			doc.querySelector('meta[name="theme-color"]').setAttribute('content', t === 'dark' ? '#0C1310' : '#0E7C66');
	}
	Array.prototype.forEach.call(doc.querySelectorAll('.rf-theme-toggle'), function (btn) {
		btn.addEventListener('click', function () {
			setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
		});
	});

	/* ---------- Header stuck ---------- */
	var header = doc.getElementById('rf-header');
	function onScroll() {
		if (!header) return;
		header.classList.toggle('is-stuck', window.scrollY > 8);
	}
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	/* ---------- Mega-Menü ---------- */
	var megaItems = doc.querySelectorAll('.rf-nav__item[data-mega]');
	var closeTimer;
	function closeAllMega(except) {
		Array.prototype.forEach.call(megaItems, function (it) {
			if (it !== except) {
				it.classList.remove('is-open');
				var b = it.querySelector('.rf-nav__link');
				if (b) b.setAttribute('aria-expanded', 'false');
			}
		});
	}
	Array.prototype.forEach.call(megaItems, function (item) {
		var btn = item.querySelector('.rf-nav__link');
		function open() { clearTimeout(closeTimer); closeAllMega(item); item.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); }
		function close() { item.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
		item.addEventListener('mouseenter', open);
		item.addEventListener('mouseleave', function () { closeTimer = setTimeout(close, 120); });
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			item.classList.contains('is-open') ? close() : open();
		});
		item.addEventListener('focusin', open);
		item.addEventListener('focusout', function (e) {
			if (!item.contains(e.relatedTarget)) close();
		});
	});
	doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAllMega(null); });
	doc.addEventListener('click', function (e) {
		if (!e.target.closest('.rf-nav__item[data-mega]')) closeAllMega(null);
	});

	/* ---------- Mobile-Panel ---------- */
	var burger = doc.querySelector('.rf-burger');
	var mobile = doc.getElementById('rf-mobile');
	var closeBtn = doc.querySelector('.rf-mobile__close');
	function openMobile() {
		mobile.classList.add('is-open');
		mobile.setAttribute('aria-hidden', 'false');
		burger.setAttribute('aria-expanded', 'true');
		doc.body.style.overflow = 'hidden';
	}
	function closeMobile() {
		mobile.classList.remove('is-open');
		mobile.setAttribute('aria-hidden', 'true');
		burger.setAttribute('aria-expanded', 'false');
		doc.body.style.overflow = '';
	}
	if (burger && mobile) {
		burger.addEventListener('click', openMobile);
		closeBtn && closeBtn.addEventListener('click', closeMobile);
		doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMobile(); });
		Array.prototype.forEach.call(mobile.querySelectorAll('a[href]'), function (a) {
			a.addEventListener('click', closeMobile);
		});
	}

	/* ---------- Mobile-Akkordeon ---------- */
	Array.prototype.forEach.call(doc.querySelectorAll('.rf-mobile .rf-acc__btn'), function (btn) {
		if (btn.tagName !== 'BUTTON') return;
		btn.addEventListener('click', function () {
			var acc = btn.closest('.rf-acc');
			var open = acc.classList.toggle('is-open');
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	});

	/* ---------- FAQ-Akkordeon ---------- */
	Array.prototype.forEach.call(doc.querySelectorAll('.rf-faq__q'), function (btn) {
		btn.addEventListener('click', function () {
			var item = btn.closest('.rf-faq__item');
			item.classList.toggle('is-open');
			btn.setAttribute('aria-expanded', item.classList.contains('is-open') ? 'true' : 'false');
		});
	});

	/* ---------- Reveal ---------- */
	var reveals = doc.querySelectorAll('.rf-reveal');
	if (reveals.length && 'IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
		Array.prototype.forEach.call(reveals, function (el) { io.observe(el); });
	} else {
		Array.prototype.forEach.call(reveals, function (el) { el.classList.add('is-in'); });
	}

	/* ---------- Hero-Befehlsleiste (Fuzzy-Suche, Levenshtein) ---------- */
	var cmd = doc.querySelector('[data-command]');
	if (cmd && window.RF_SEARCH) {
		var input = cmd.querySelector('input');
		var box = cmd.querySelector('.rf-command__results');
		var idx = window.RF_SEARCH;
		var active = -1;
		var MAX_RESULTS = 8;      // maximale Anzahl Vorschläge
		var SIMILAR_MIN = 0.34;   // Untergrenze, damit «ähnliche» Treffer noch relevant sind

		/* Normalisiert Text: Kleinbuchstaben, Diakritika/Umlaute-Akzente weg, Satzzeichen zu Leerzeichen. */
		function norm(s) {
			return (s || '').toLowerCase()
				.normalize('NFD').replace(/[̀-ͯ]/g, '')
				.replace(/[«»„“”"'’.,;:!?()\/\-]/g, ' ')
				.replace(/\s+/g, ' ').trim();
		}

		/* Levenshtein-Distanz (Editierabstand) zwischen zwei Zeichenketten. */
		function levenshtein(a, b) {
			if (a === b) return 0;
			var al = a.length, bl = b.length;
			if (!al) return bl;
			if (!bl) return al;
			var prev = new Array(bl + 1), cur = new Array(bl + 1), i, j;
			for (j = 0; j <= bl; j++) prev[j] = j;
			for (i = 1; i <= al; i++) {
				cur[0] = i;
				var ca = a.charCodeAt(i - 1);
				for (j = 1; j <= bl; j++) {
					var cost = ca === b.charCodeAt(j - 1) ? 0 : 1;
					var m = prev[j] + 1, n = cur[j - 1] + 1, o = prev[j - 1] + cost;
					cur[j] = m < n ? (m < o ? m : o) : (n < o ? n : o);
				}
				var tmp = prev; prev = cur; cur = tmp;
			}
			return prev[bl];
		}

		/* Ähnlichkeit 0..1 des Suchbegriffs q gegen ein Zielwort, mit Präfix-/Teilstring-Bonus. */
		function similarity(q, target) {
			if (!q || !target) return 0;
			var pos = target.indexOf(q);
			if (pos === 0) return 1;      // Zielwort beginnt mit q
			if (pos > -1) return 0.93;    // q kommt irgendwo vor
			var dist = levenshtein(q, target);
			// Teilweise getippt: q gegen ein gleich langes Präfix des Ziels vergleichen.
			if (target.length > q.length) {
				var win = levenshtein(q, target.slice(0, q.length));
				if (win < dist) dist = win;
			}
			return 1 - dist / Math.max(q.length, target.length);
		}

		/* Bewertet einen Indexeintrag gegen die (normalisierten) Suchwörter. */
		function scoreItem(qWords, qFull, item) {
			var label = norm(item.label);
			var words = label.split(' ');
			var cat = norm(item.cat);
			var whole = similarity(qFull, label);          // ganzer Begriff gegen ganzes Label
			var sum = 0;
			for (var i = 0; i < qWords.length; i++) {
				var best = similarity(qWords[i], cat);
				for (var j = 0; j < words.length; j++) {
					var s = similarity(qWords[i], words[j]);
					if (s > best) best = s;
				}
				sum += best;
			}
			var avg = sum / qWords.length;                 // je mehr Wörter passen, desto höher
			return avg > whole ? avg : whole;
		}

		/* Liefert die Treffer nach Nähe sortiert: das Nächstliegende zuerst, dann Ähnliches. */
		function search(raw) {
			var qFull = norm(raw);
			if (!qFull) return [];
			var qWords = qFull.split(' ');
			var scored = idx.map(function (item) {
				return { item: item, score: scoreItem(qWords, qFull, item) };
			});
			scored.sort(function (a, b) {
				if (b.score !== a.score) return b.score - a.score;
				return a.item.label.length - b.item.label.length;
			});
			var out = [];
			for (var k = 0; k < scored.length && out.length < MAX_RESULTS; k++) {
				// Immer mindestens den nächstliegenden Treffer zeigen; danach nur noch Ähnliches.
				if (out.length >= 1 && scored[k].score < SIMILAR_MIN) break;
				out.push(scored[k].item);
			}
			return out;
		}

		function esc(s) {
			return String(s).replace(/[&<>"]/g, function (c) {
				return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
			});
		}
		/* Hebt den exakt getippten Teil im Label hervor (falls vorhanden). */
		function highlight(label, raw) {
			var q = raw.trim();
			if (!q) return esc(label);
			var pos = label.toLowerCase().indexOf(q.toLowerCase());
			if (pos < 0) return esc(label);
			return esc(label.slice(0, pos)) +
				'<mark>' + esc(label.slice(pos, pos + q.length)) + '</mark>' +
				esc(label.slice(pos + q.length));
		}

		function render(list) {
			if (!input.value.trim()) { box.hidden = true; box.innerHTML = ''; return; }
			if (!list.length) { box.hidden = false; box.innerHTML = '<div class="rf-command__none">Nichts gefunden — schauen Sie in den Kategorien oben.</div>'; return; }
			box.hidden = false;
			var raw = input.value;
			box.innerHTML = list.map(function (r, i) {
				return '<a href="' + esc(r.url) + '" data-i="' + i + '">' +
					'<span class="rf-res-label">' + highlight(r.label, raw) + '</span>' +
					'<span class="rf-res-cat">' + esc(r.cat) + '</span></a>';
			}).join('');
		}
		function update() {
			active = -1;
			render(search(input.value));
		}
		input.addEventListener('input', update);
		input.addEventListener('focus', update);
		input.addEventListener('keydown', function (e) {
			var links = box.querySelectorAll('a');
			if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, links.length - 1); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); }
			else if (e.key === 'Enter') { if (links[active]) { window.location = links[active].href; } return; }
			else return;
			links.forEach(function (l, i) {
				var on = i === active;
				l.classList.toggle('is-active', on);
				if (on) l.scrollIntoView({ block: 'nearest' });
			});
		});
		doc.addEventListener('click', function (e) {
			if (!cmd.contains(e.target)) { box.hidden = true; }
		});
	}
})();
