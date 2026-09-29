(function () {
	'use strict';
	if (typeof termimalVotes === 'undefined') return;
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.termimal-helpful__btn');
		if (!btn) return;
		var wrap = btn.closest('.termimal-helpful');
		if (!wrap) return;
		var id = wrap.getAttribute('data-comment');
		if (!id) return;
		btn.disabled = true;
		var body = new FormData();
		body.append('action', 'termimal_helpful');
		body.append('nonce', termimalVotes.nonce);
		body.append('comment_id', id);
		fetch(termimalVotes.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data && data.success && data.data) {
					var countEl = wrap.querySelector('.termimal-helpful__count');
					if (countEl) countEl.textContent = data.data.label;
					btn.textContent = '\u2713';
				}
				btn.disabled = false;
			})
			.catch(function () { btn.disabled = false; });
	});
})();
