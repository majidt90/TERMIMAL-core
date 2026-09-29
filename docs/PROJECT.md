# TERMIMAL Portfolio — Project Documentation

**Company:** TERMIMAL (AI tools & technology)  
**Stack:** WordPress Block Theme + TERMIMAL Core plugin  
**Versions:** Plugin **2.9.0** · Theme **1.5.0**  
**Languages:** English + Persian (fa_IR)  
**WooCommerce:** Not required (fully independent)

---

## 1. Overview

TERMIMAL Portfolio is a modern WordPress product showcase for AI-powered tools. It includes:

- Custom post type `termimal_product` with rich meta (status, gallery, FAQ, version, related products, CTAs)
- Block theme (FSE) with dark cyan brand identity, particle backgrounds, Audiowide + Vazirmatn fonts
- Gutenberg **Product Studio** sidebar (cover image, details, gallery)
- Front-end filters, cards, single product layout, Q&A comments
- Auth: register, login, profile, newsletter
- Optional Polylang, GA4 events, webhooks, lead form
- CSV import/export, Product Editor role, Gutenberg product blocks

---

## 2. Installation

1. Install WordPress 6.0+ (PHP 7.4+)
2. Upload and activate **termimal** theme
3. Upload and activate **TERMIMAL Core** plugin
4. Settings → General → enable **Anyone can register** (if using front-end signup)
5. Settings → TERMIMAL → configure particles, GA4, webhooks, registration
6. Optional: install **Polylang** for dual-language content mapping

Packages: `termimal-theme.zip` · `termimal-core-plugin.zip`

---

## 3. Architecture

```
termimal/theme/          Block theme (FSE)
  templates/             single, archive, pages (profile, auth, contact…)
  parts/                 header, footer
  patterns/              product + page patterns (Gutenberg-safe)
  assets/css|js          main.css, particles.js

termimal/plugin/         TERMIMAL Core
  includes/              CPT, Meta, Frontend, Auth, Comments, Leads…
  assets/js              admin-gutenberg.js, admin-product.js, frontend.js
  blocks/                product-grid, product-card, product-faq, product-changelog
  languages/             termimal-fa_IR.po/.mo
```

---

## 4. Product Studio (Add / Edit Product)

**Gutenberg sidebar panels**

| Panel | Fields |
|-------|--------|
| TERMIMAL Cover | Select / upload cover → `featured_media` |
| TERMIMAL Details | Status, Featured, Year, Visit/Demo/Docs, Tech, Version, Changelog |
| TERMIMAL Gallery | Multi-image album |

**Also (meta boxes):** FAQ, Related products, Publish checklist, Categories + Excerpt

**Status:** `live` · `beta` · `coming_soon` · `archived` (bulk actions available)

---

## 5. Front-end features

| Feature | Notes |
|---------|--------|
| Product archive `/product/` | Filters + card grid |
| Single product | Cover, meta chips, CTAs, gallery lightbox, FAQ, related, discussion |
| Cards | Image, status badge, excerpt, View product |
| Comments / Q&A | Unified form; auto-detect question; replies → answers |
| Particles | Network / Dots / Stars · density · on/off |
| i18n | EN/FA switcher, RTL, fonts |
| Auth nav | Log in · Sign up · Profile |

---

## 6. Shortcodes

`[termimal_register]` `[termimal_login]` `[termimal_profile]` `[termimal_newsletter]` `[termimal_auth]` `[termimal_account_nav]` `[termimal_filters]` `[termimal_products]` `[termimal_lead]` `[termimal_contact]` `[termimal_lang_switcher]`

Auto pages: `/login/`, `/register/`, `/profile/`

---

## 7. Settings (Settings → TERMIMAL)

Products per page, related, comments options, GA4, webhooks, registration, particle enable/density/style.

---

## 8. Brand

- Primary: `#00F0FF`
- Surfaces: `#050507` / `#0C0C0E`
- Fonts: Audiowide (EN), Vazirmatn (FA)

---

## 9. Patterns

Product: Intro hero, Minimal, Features grid, Story + bullets, CTA strip (no invalid post-title/post-excerpt in content).

---

## 10. Security & performance

Nonces, capabilities, honeypots, lazy images, reduced-motion, soft Polylang dependency.

---

## 11. Changelog (high level)

| Version | Highlights |
|---------|------------|
| 2.9 | Gutenberg Product Studio + REST meta |
| 2.8 | Cover/gallery media pickers |
| 2.7 | Product Studio admin UI |
| 2.6 | Account nav, particle density/model |
| 2.5 | Particles, page templates, auth/profile |
| 2.4 | Registration + newsletter |
| Theme 1.5 | Fixed product patterns for Gutenberg |

---

## 12. License

GPL-2.0-or-later
