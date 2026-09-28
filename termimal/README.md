# TERMIMAL Portfolio Theme + Core Plugin

Modern, dark-first WordPress Block Theme and companion plugin for the TERMIMAL AI-tools portfolio.

**Version:** Theme 1.0.0 · Plugin 1.0.1

## Quick Install (recommended)

Download the separate packages and upload via WordPress admin:

| Package | Upload to |
|---------|-----------|
| `termimal-core-plugin.zip` | **Plugins → Add New → Upload** |
| `termimal-theme.zip` | **Appearance → Themes → Add New → Upload** |

After activation:
1. Activate **TERMIMAL Core** plugin
2. Activate **TERMIMAL Portfolio** theme
3. Go to **Products → Add New**

### Expected folder structure after install

```
wp-content/plugins/termimal-core/
  ├── termimal-core.php
  ├── includes/
  └── assets/

wp-content/themes/termimal/
  ├── style.css
  ├── theme.json
  ├── functions.php
  ├── screenshot.png
  ├── templates/
  ├── parts/
  └── assets/
```

## Features

- Product CPT (`termimal_product`) with archive + single templates
- External product link
- Multi-image gallery / album
- Related products linking
- Admin settings (Settings → TERMIMAL)
- Brand design tokens (dark-first, cyan `#00F0FF`)
- Light mode style variation
- CSS animations (aurora + blur-in, reduced-motion safe)

## Manual install from this repo

```bash
# Plugin
cp -r termimal/plugin /path/to/wp-content/plugins/termimal-core

# Theme
cp -r termimal/theme /path/to/wp-content/themes/termimal
```

## Changelog

### Plugin 1.0.1
- Fix: register CPT on `init` (prevents critical error)
- Defensive file loading (missing includes no longer white-screen the site)
- Requires PHP 7.4+, WordPress 6.0+

### 1.0.0
- Initial release

## Design System

See `docs/brand/` for full tokens extracted from the `</TERMIMAL>` logo.
