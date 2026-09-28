# TERMIMAL-core

**Knowledge base, design system, and skills for the TERMIMAL portfolio project.**

TERMIMAL is a technology company focused on AI-powered tools. This repository holds the structured documentation, brand identity, and callable skills required to build a modern WordPress portfolio theme and related frontend experiences.

---

## Repository Structure

```
TERMIMAL-core/
├── docs/
│   ├── wordpress/           # Phase 1 — WordPress mastery
│   │   ├── 01-core.md
│   │   ├── 02-plugin-handbook.md
│   │   ├── 03-theme-handbook.md
│   │   ├── 04-rest-api.md
│   │   ├── 05-block-editor.md
│   │   ├── 06-woocommerce.md
│   │   └── 07-security-performance.md
│   ├── react-bits/          # Phase 2 — React Bits skill
│   │   ├── skill-overview.md
│   │   ├── components-index.md
│   │   ├── decision-matrix.md
│   │   └── snippets/
│   └── brand/               # Phase 2.5 — Logo-derived design system
│       ├── brand-tokens.md
│       └── design-system.md
├── skills/                  # Callable agent skills
│   ├── wordpress-skill.md
│   ├── react-bits-skill.md
│   └── brand-skill.md
├── prompts/
│   └── master-prompt.md
└── README.md
```

---

## Brand Snapshot

Logo: `</TERMIMAL>`  
Mood: Tech / Terminal / Precise / Dark-first / Minimal  
Primary accent: Cyan `#00F0FF`  
Default mode: Dark (`#0A0A0A`)

Full tokens and usage rules → `docs/brand/`

---

## How to Use

1. Read `prompts/master-prompt.md` for the governing rules.
2. Consult the relevant skill in `skills/` before generating code.
3. Follow the decision matrix in `docs/react-bits/decision-matrix.md` when adding motion.
4. Keep product data and relationships in a companion plugin; keep the theme focused on presentation.

---

## Phase Status

| Phase | Description                          | Status |
|-------|--------------------------------------|--------|
| 1     | Full WordPress Mastery               | ✅     |
| 2     | React Bits as a Skill                | ✅     |
| 2.5   | Logo Analysis & Brand Extraction     | ✅     |
| 3     | Persist to this repository           | ✅     |

---

## License

Private / internal use for the TERMIMAL project.
