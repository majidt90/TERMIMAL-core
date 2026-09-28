(function () {
	'use strict';
	function initLightbox() {
		var gallery = document.querySelector('.termimal-gallery');
		if (!gallery) return;
		var images = gallery.querySelectorAll('img');
		if (!images.length) return;
		var sources = [];
		images.forEach(function (img) {
			sources.push(img.currentSrc || img.src);
			img.setAttribute('tabindex', '0');
			img.setAttribute('role', 'button');
		});
		var overlay = document.createElement('div');
		overlay.className = 'termimal-lightbox';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.innerHTML =
			'<button type="button" class="termimal-lightbox__close" aria-label="Close">&times;</button>' +
			'<button type="button" class="termimal-lightbox__prev" aria-label="Previous">‹</button>' +
			'<img class="termimal-lightbox__img" alt="">' +
			'<button type="button" class="termimal-lightbox__next" aria-label="Next">›</button>';
		document.body.appendChild(overlay);
		var imgEl = overlay.querySelector('.termimal-lightbox__img');
		var idx = 0;
		function open(i) {
			idx = i;
			imgEl.src = sources[idx];
			overlay.classList.add('is-open');
			document.body.style.overflow = 'hidden';
		}
		function close() {
			overlay.classList.remove('is-open');
			document.body.style.overflow = '';
			imgEl.removeAttribute('src');
		}
		function prev() {
			idx = (idx - 1 + sources.length) % sources.length;
			imgEl.src = sources[idx];
		}
		function next() {
			idx = (idx + 1) % sources.length;
			imgEl.src = sources[idx];
		}
		images.forEach(function (img, i) {
			img.addEventListener('click', function () { open(i); });
			img.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); }
			});
		});
		overlay.querySelector('.termimal-lightbox__close').addEventListener('click', close);
		overlay.querySelector('.termimal-lightbox__prev').addEventListener('click', prev);
		overlay.querySelector('.termimal-lightbox__next').addEventListener('click', next);
		overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
		document.addEventListener('keydown', function (e) {
			if (!overlay.classList.contains('is-open')) return;
			if (e.key === 'Escape') close();
			if (e.key === 'ArrowLeft') prev();
			if (e.key === 'ArrowRight') next();
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initLightbox);
	} else {
		initLightbox();
	}
})();
