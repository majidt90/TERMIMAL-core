# TERMIMAL Portfolio Theme + Core Plugin

Modern, dark-first WordPress Block Theme and companion plugin for the TERMIMAL AI-tools portfolio.

## Contents

```
termimal/
├── theme/                  # Block Theme (TERMIMAL Portfolio)
│   ├── style.css
│   ├── theme.json          # Design tokens from brand system
│   ├── functions.php
│   ├── templates/
│   │   ├── index.html
│   │   ├── archive-termimal_product.html
│   │   └── single-termimal_product.html
│   ├── parts/
│   │   ├── header.html
│   │   └── footer.html
│   └── assets/
└── plugin/                 # Companion plugin (TERMIMAL Core)
    ├── termimal-core.php
    ├── includes/
    │   ├── class-cpt.php       # Product CPT
    │   ├── class-meta.php      # URL, Gallery, Related products
    │   └── class-admin.php     # Settings page
    └── assets/js/admin-gallery.js
```

## Features

- **Product CPT** (`termimal_product`) with archive + single templates
- **Product Details**: external link / URL
- **Gallery / Album**: multi-image media uploader
- **Related Products**: multi-select linking between products
- **Admin Settings** page under Settings → TERMIMAL
- **Brand-aligned design tokens** (dark-first, cyan primary `#00F0FF`, Space Grotesk + Inter)
- **Block Theme** with Full Site Editing support
- Accessibility & reduced-motion ready

## Installation

1. Copy `theme/` folder into `wp-content/themes/termimal` (or zip and upload).
2. Copy `plugin/` folder into `wp-content/plugins/termimal-core` (or zip and upload).
3. Activate the **TERMIMAL Core** plugin.
4. Activate the **TERMIMAL Portfolio** theme.
5. Go to **Products → Add New** and start adding products.
6. Optionally configure **Settings → TERMIMAL**.

## Adding a Product

1. Title + Description (Block Editor)
2. Set Featured Image (cover)
3. Product Details box → External Link
4. Gallery box → Add multiple images for the album
5. Related Products sidebar → select other products to link

## Design System

All colors, typography and radii come from the Phase 2.5 brand extraction (`</TERMIMAL>` logo).  
See `docs/brand/` for the full token set.

## Next Steps (optional enhancements)

- Front-end rendering of gallery & related products on single template (via shortcode or block)
- React Bits integration (Soft Aurora / BlurText) limited to 2–3 components per page
- Style variations for light mode
- Product pattern library

---

Built according to the TERMIMAL-core knowledge base (Phases 1–3).
