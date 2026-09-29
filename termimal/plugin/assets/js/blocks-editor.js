(function (wp) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var ToggleControl = wp.components.ToggleControl;
	var TextControl = wp.components.TextControl;
	var ServerSideRender = wp.serverSideRender;
	var __ = wp.i18n.__;
	registerBlockType('termimal/product-grid', {
		apiVersion: 2,
		title: __('TERMIMAL Product Grid', 'termimal'),
		icon: 'portfolio',
		category: 'widgets',
		attributes: {
			limit: { type: 'number', default: 6 },
			columns: { type: 'number', default: 3 },
			featured: { type: 'boolean', default: false },
			category: { type: 'string', default: '' },
			status: { type: 'string', default: '' }
		},
		edit: function (props) {
			var attrs = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps({ className: 'termimal-block-grid-editor' });
			return el('div', blockProps,
				el(InspectorControls, null,
					el(PanelBody, { title: __('Grid settings', 'termimal'), initialOpen: true },
						el(RangeControl, { label: __('Number of products', 'termimal'), value: attrs.limit, onChange: function (v) { setAttributes({ limit: v }); }, min: 1, max: 24 }),
						el(RangeControl, { label: __('Columns', 'termimal'), value: attrs.columns, onChange: function (v) { setAttributes({ columns: v }); }, min: 1, max: 4 }),
						el(ToggleControl, { label: __('Featured only', 'termimal'), checked: !!attrs.featured, onChange: function (v) { setAttributes({ featured: v }); } }),
						el(TextControl, { label: __('Category slug', 'termimal'), value: attrs.category || '', onChange: function (v) { setAttributes({ category: v }); } }),
						el(TextControl, { label: __('Status', 'termimal'), value: attrs.status || '', onChange: function (v) { setAttributes({ status: v }); }, help: __('live, beta, coming_soon, or archived', 'termimal') })
					)
				),
				el(ServerSideRender, { block: 'termimal/product-grid', attributes: attrs })
			);
		},
		save: function () { return null; }
	});
})(window.wp);
