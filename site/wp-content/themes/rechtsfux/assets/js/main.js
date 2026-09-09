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

	/* ---------- Hero-Befehlsleiste (Suche) ---------- */
	var cmd = doc.querySelector('[data-command]');
	if (cmd && window.RF_SEARCH) {
		var input = cmd.querySelector('input');
		var box = cmd.querySelector('.rf-command__results');
		var idx = window.RF_SEARCH;
		var active = -1;

		function render(list) {
			if (!input.value.trim()) { box.hidden = true; box.innerHTML = ''; return; }
			if (!list.length) { box.hidden = false; box.innerHTML = '<div class="rf-command__none">Nichts gefunden — schauen Sie in den Kategorien oben.</div>'; return; }
			box.hidden = false;
			box.innerHTML = list.map(function (r, i) {
				return '<a href="' + r.url + '" data-i="' + i + '">' +
					'<span>' + r.label + '</span>' +
					'<span class="rf-res-cat">' + r.cat + '</span></a>';
			}).join('');
		}
		function search(q) {
			q = q.toLowerCase();
			return idx.filter(function (r) {
				return r.label.toLowerCase().indexOf(q) > -1 || r.cat.toLowerCase().indexOf(q) > -1;
			}).slice(0, 7);
		}
		var results = [];
		function update() {
			results = search(input.value);
			active = -1;
			render(results);
		}
		input.addEventListener('input', update);
		input.addEventListener('focus', update);
		input.addEventListener('keydown', function (e) {
			var links = box.querySelectorAll('a');
			if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, links.length - 1); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); }
			else if (e.key === 'Enter') { if (links[active]) { window.location = links[active].href; } return; }
			else return;
			links.forEach(function (l, i) { l.classList.toggle('is-active', i === active); });
		});
		doc.addEventListener('click', function (e) {
			if (!cmd.contains(e.target)) { box.hidden = true; }
		});
	}
})();
