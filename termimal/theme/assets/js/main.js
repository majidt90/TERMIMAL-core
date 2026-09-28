/**
 * TERMIMAL front-end interactions.
 * Keep lightweight. Heavy animations come from carefully chosen CSS effects.
 */
(function () {
	'use strict';

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (prefersReducedMotion) {
		document.documentElement.classList.add('reduced-motion');
	}

	document.addEventListener('DOMContentLoaded', function () {
		// Future: initialize any lightweight non-React Bits effects here.
	});
})();
