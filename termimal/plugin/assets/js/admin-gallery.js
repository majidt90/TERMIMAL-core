/**
 * TERMIMAL Product Gallery media uploader.
 */
(function ($) {
	'use strict';

	$(function () {
		var frame;
		var $input = $('#termimal_product_gallery');
		var $preview = $('.termimal-gallery-preview');

		$('#termimal-add-gallery').on('click', function (e) {
			e.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Select Product Gallery Images',
				button: { text: 'Use these images' },
				multiple: true,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				var selection = frame.state().get('selection');
				var ids = [];
				$preview.empty();

				selection.each(function (attachment) {
					attachment = attachment.toJSON();
					ids.push(attachment.id);
					var url = (attachment.sizes && attachment.sizes.thumbnail)
						? attachment.sizes.thumbnail.url
						: attachment.url;
					$preview.append(
						'<li data-id="' + attachment.id + '" style="position:relative;">' +
						'<img src="' + url + '" width="80" height="80" style="object-fit:cover;border-radius:4px;">' +
						'</li>'
					);
				});

				$input.val(ids.join(','));
			});

			frame.open();
		});

		$('#termimal-clear-gallery').on('click', function (e) {
			e.preventDefault();
			$input.val('');
			$preview.empty();
		});
	});
})(jQuery);
