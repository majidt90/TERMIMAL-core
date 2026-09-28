# TERMIMAL Brand Tokens

**Source Logo**: `1790587215968-01a0e74e-bf90-74e6-93f7-bb9ca58a9804.jpeg` + `download.png`  
**Analyzed**: 2026-09-28  
**Phase**: 2.5 — Logo Analysis & Brand Identity Extraction

---

## 1. Logo Description

The logo is a bold, geometric wordmark:

```
</TERMIMAL>
```

- Stylized as a closing HTML/terminal tag
- Left chevron `<` + slash `/` + uppercase **TERMIMAL** + right chevron `>`
- Pure black (#000000) on white (or inverted)
- High-contrast, monospaced/tech-inspired letterforms
- Horizontal, compact, strong presence

This immediately signals: **terminal / code / AI tools / precision technology**.

---

## 2. Color Palette (Derived)

Because the logo itself is strictly monochrome, the palette is built as a **monochrome core + purposeful tech accents**.

### Core (from logo)
| Token            | HEX       | RGB              | HSL                  | Usage                     |
|------------------|-----------|------------------|----------------------|---------------------------|
| `black`          | `#000000` | 0, 0, 0          | 0 0% 0%              | Primary text, logo, dark surfaces |
| `white`          | `#FFFFFF` | 255, 255, 255    | 0 0% 100%            | Light mode bg, contrast text |
| `gray-950`       | `#0A0A0A` | 10, 10, 10       | 0 0% 4%              | Darkest background        |
| `gray-900`       | `#111111` | 17, 17, 17       | 0 0% 7%              | Dark surface              |
| `gray-800`       | `#1A1A1A` | 26, 26, 26       | 0 0% 10%             | Elevated dark surface     |
| `gray-700`       | `#2A2A2A` | 42, 42, 42       | 0 0% 16%             | Borders (dark)            |
| `gray-400`       | `#A3A3A3` | 163, 163, 163    | 0 0% 64%             | Secondary text            |
| `gray-200`       | `#E5E5E5` | 229, 229, 229    | 0 0% 90%             | Light borders / muted     |
| `gray-50`        | `#FAFAFA` | 250, 250, 250    | 0 0% 98%             | Light mode background     |

### Accent (Tech / AI inspired — not in logo, derived for identity)
| Token            | HEX       | RGB              | HSL                  | Semantic                  |
|------------------|-----------|------------------|----------------------|---------------------------|
| `primary`        | `#00F0FF` | 0, 240, 255      | 184 100% 50%         | Cyan — main interactive   |
| `primary-dim`    | `#00B8C4` | 0, 184, 196      | 184 100% 38%         | Hover / pressed           |
| `secondary`      | `#7B61FF` | 123, 97, 255     | 250 100% 69%         | Purple — secondary accent |
| `accent`         | `#00FF9D` | 0, 255, 157      | 157 100% 50%         | Neon green — success / highlight |
| `warning`        | `#FFB800` | 255, 184, 0      | 43 100% 50%          | Warning                   |
| `error`          | `#FF3B5C` | 255, 59, 92      | 348 100% 62%         | Error                     |

### Semantic Tokens
```
--color-background:        var(--gray-950)     /* Dark default */
--color-surface:           var(--gray-900)
--color-surface-elevated:  var(--gray-800)
--color-text:              var(--white)
--color-text-muted:        var(--gray-400)
--color-border:            var(--gray-700)
--color-primary:           var(--primary)
--color-primary-hover:     var(--primary-dim)
```

---

## 3. Typography

**Style from logo**: Geometric, bold, uppercase-friendly, tech/terminal feel.  
Slightly condensed, high x-height, clean terminals.

### Recommended Font Stack
| Role              | Font                    | Fallback                        | Weight     |
|-------------------|-------------------------|---------------------------------|------------|
| **Display / Logo**| `Space Grotesk` or `Orbitron` | system-ui, sans-serif          | 700        |
| **Headings**      | `Inter` or `Geist Sans` | system-ui                      | 600–700    |
| **Body**          | `Inter`                 | system-ui                      | 400–500    |
| **Mono / Code**   | `JetBrains Mono` / `Fira Code` | ui-monospace                 | 400–500    |

**Rationale**:  
- Space Grotesk / Orbitron echo the geometric, futuristic letterforms of the logo.  
- Inter provides excellent readability for product descriptions.  
- Mono font reinforces the “terminal” identity.

### Type Scale (suggested)
```
--text-xs:   0.75rem
--text-sm:   0.875rem
--text-base: 1rem
--text-lg:   1.125rem
--text-xl:   1.25rem
--text-2xl:  1.5rem
--text-3xl:  1.875rem
--text-4xl:  2.25rem
--text-5xl:  3rem
--text-6xl:  3.75rem
```

---

## 4. Form Language

| Property          | Value / Guidance                          |
|-------------------|-------------------------------------------|
| Corner radius     | Small → medium (4px – 12px). Avoid large rounded “soft” UI. |
| Line weight       | Medium-bold for icons and borders         |
| Symmetry          | Strong horizontal axis (logo is wide)     |
| Negative space    | Generous, clean, high contrast            |
| Geometry          | Sharp chevrons, angular accents, clean rectangles |
| Motifs            | `< > /` shapes, terminal cursors, subtle grid, scan-lines |

---

## 5. Visual Mood

**Primary mood**: **Tech / Terminal / Precise / Modern / Minimal / Dark-first**

- Cyber-adjacent but professional (not neon-club)
- High contrast
- Calm confidence of serious AI tooling
- “Code meets product”

**Avoid**: Soft pastel, heavy skeuomorphism, playful cartoon, pure brutalist chaos.

---

## 6. Dark / Light Modes

| Mode   | Default? | Background     | Text          | Accent usage                  |
|--------|----------|----------------|---------------|-------------------------------|
| **Dark**   | **Yes**  | `#0A0A0A`     | `#FFFFFF`    | Cyan / Purple glow sparingly |
| **Light**  | Secondary| `#FAFAFA`     | `#000000`    | Same accents, slightly desaturated |

Logo works perfectly inverted (white wordmark on dark, black on light).

---

## 7. Reusable Motifs

1. **Chevron / Angle brackets** (`< >`) — navigation arrows, decorative frames, section dividers
2. **Slash `/`** — subtle separators, loading indicators
3. **Terminal cursor** (blinking block or underscore)
4. **Horizontal scan / grid lines** — background texture at very low opacity
5. **Monospace code snippets** as decorative elements in product cards
6. **Sharp geometric progress bars** or status indicators

These motifs should appear in React Bits backgrounds, product cards, and micro-interactions.

---

**Status**: ✅ Brand Tokens extracted and documented
