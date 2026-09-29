(function () {
	'use strict';
	if (typeof termimalAnalytics === 'undefined' || !termimalAnalytics.enabled) return;
	function track(name, params) {
		params = params || {};
		try { if (typeof gtag === 'function' && termimalAnalytics.ga4) { gtag('event', name, params); } } catch (e) {}
		try { window.dataLayer = window.dataLayer || []; window.dataLayer.push(Object.assign({ event: name }, params)); } catch (e2) {}
	}
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a.termimal-cta, a.wp-block-button__link');
		if (!a) return;
		var href = a.getAttribute('href') || '';
		var label = (a.textContent || '').trim();
		var type = 'cta';
		if (a.classList.contains('termimal-cta--primary') || /visit/i.test(label)) type = 'visit_product';
		else if (a.classList.contains('termimal-cta--demo') || /demo/i.test(label)) type = 'try_demo';
		else if (a.classList.contains('termimal-cta--docs') || /doc/i.test(label)) type = 'view_docs';
		else if (!a.classList.contains('termimal-cta')) return;
		track(type, { event_category: 'termimal_product', event_label: label, link_url: href, page_path: window.location.pathname });
	});
})();
