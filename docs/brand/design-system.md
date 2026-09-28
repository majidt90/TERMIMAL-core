# TERMIMAL Design System

**Derived from logo analysis** · Phase 2.5  
**Companion file**: `brand-tokens.md`

---

## 1. Design Tokens (CSS Variables)

```css
:root {
  /* ===== Colors ===== */
  --color-black: #000000;
  --color-white: #FFFFFF;

  --gray-950: #0A0A0A;
  --gray-900: #111111;
  --gray-800: #1A1A1A;
  --gray-700: #2A2A2A;
  --gray-400: #A3A3A3;
  --gray-200: #E5E5E5;
  --gray-50:  #FAFAFA;

  --primary:        #00F0FF;
  --primary-dim:    #00B8C4;
  --secondary:      #7B61FF;
  --accent:         #00FF9D;
  --warning:        #FFB800;
  --error:          #FF3B5C;

  /* Semantic */
  --background:           var(--gray-950);
  --surface:              var(--gray-900);
  --surface-elevated:     var(--gray-800);
  --text:                 var(--color-white);
  --text-muted:           var(--gray-400);
  --border:               var(--gray-700);
  --primary-color:        var(--primary);
  --primary-hover:        var(--primary-dim);

  /* ===== Spacing ===== */
  --space-1:  0.25rem;
  --space-2:  0.5rem;
  --space-3:  0.75rem;
  --space-4:  1rem;
  --space-5:  1.5rem;
  --space-6:  2rem;
  --space-8:  3rem;
  --space-10: 4rem;
  --space-12: 6rem;

  /* ===== Radius ===== */
  --radius-sm:  4px;
  --radius-md:  8px;
  --radius-lg:  12px;
  --radius-xl:  16px;
  --radius-full: 9999px;

  /* ===== Shadows ===== */
  --shadow-sm:  0 1px 2px rgba(0, 0, 0, 0.4);
  --shadow-md:  0 4px 12px rgba(0, 0, 0, 0.5);
  --shadow-lg:  0 8px 24px rgba(0, 0, 0, 0.6);
  --shadow-glow-primary: 0 0 20px rgba(0, 240, 255, 0.25);

  /* ===== Motion ===== */
  --ease-out:      cubic-bezier(0.16, 1, 0.3, 1);
  --ease-in-out:   cubic-bezier(0.65, 0, 0.35, 1);
  --duration-fast: 150ms;
  --duration-normal: 250ms;
  --duration-slow: 400ms;
}

[data-theme="light"] {
  --background:       var(--gray-50);
  --surface:          var(--color-white);
  --surface-elevated: var(--gray-200);
  --text:             var(--color-black);
  --text-muted:       #525252;
  --border:           var(--gray-200);
}
```

See full file in local TERMIMAL-core for Tailwind config and component examples.

**Status**: ✅ Design System documented
