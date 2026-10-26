---
name: Bibliotheca & Vinyl Archive
colors:
  surface: '#1a0935'
  surface-dim: '#1a0935'
  surface-bright: '#41315d'
  surface-container-lowest: '#150330'
  surface-container-low: '#23123d'
  surface-container: '#271642'
  surface-container-high: '#32214d'
  surface-container-highest: '#3d2c58'
  on-surface: '#ecdcff'
  on-surface-variant: '#cfc2d5'
  inverse-surface: '#ecdcff'
  inverse-on-surface: '#382854'
  outline: '#988d9e'
  outline-variant: '#4c4353'
  surface-tint: '#deb7ff'
  primary: '#deb7ff'
  on-primary: '#4a007f'
  primary-container: '#7b2cbf'
  on-primary-container: '#e4c2ff'
  inverse-primary: '#8234c6'
  secondary: '#e1b6ff'
  on-secondary: '#4c007b'
  secondary-container: '#691a9f'
  on-secondary-container: '#d69eff'
  tertiary: '#e5b5ff'
  on-tertiary: '#481965'
  tertiary-container: '#734490'
  on-tertiary-container: '#e9c0ff'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#f1dbff'
  primary-fixed-dim: '#deb7ff'
  on-primary-fixed: '#2d0050'
  on-primary-fixed-variant: '#680eac'
  secondary-fixed: '#f2daff'
  secondary-fixed-dim: '#e1b6ff'
  on-secondary-fixed: '#2e004d'
  on-secondary-fixed-variant: '#691a9f'
  tertiary-fixed: '#f4d9ff'
  tertiary-fixed-dim: '#e5b5ff'
  on-tertiary-fixed: '#30004b'
  on-tertiary-fixed-variant: '#60327d'
  background: '#1a0935'
  on-background: '#ecdcff'
  surface-variant: '#3d2c58'
typography:
  display-lg:
    fontFamily: Quicksand
    fontSize: 3rem
    fontWeight: '700'
    lineHeight: 3.5rem
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Quicksand
    fontSize: 2rem
    fontWeight: '700'
    lineHeight: 2.5rem
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Quicksand
    fontSize: 2rem
    fontWeight: '600'
    lineHeight: 2.5rem
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Quicksand
    fontSize: 1.5rem
    fontWeight: '600'
    lineHeight: 2rem
  headline-sm:
    fontFamily: Quicksand
    fontSize: 1.25rem
    fontWeight: '600'
    lineHeight: 1.75rem
  title-editorial:
    fontFamily: Newsreader
    fontSize: 1.125rem
    fontWeight: '500'
    lineHeight: 1.625rem
  body-lg:
    fontFamily: Hanken Grotesk
    fontSize: 1rem
    fontWeight: '400'
    lineHeight: 1.5rem
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 0.875rem
    fontWeight: '400'
    lineHeight: 1.25rem
  body-sm:
    fontFamily: Hanken Grotesk
    fontSize: 0.75rem
    fontWeight: '400'
    lineHeight: 1rem
  tabular-data:
    fontFamily: Roboto Flex
    fontSize: 0.8125rem
    fontWeight: '500'
    lineHeight: 1.125rem
    letterSpacing: 0.01em
  label-code:
    fontFamily: Roboto Flex
    fontSize: 0.6875rem
    fontWeight: '600'
    lineHeight: 0.875rem
    letterSpacing: 0.06em
  label-md:
    fontFamily: Hanken Grotesk
    fontSize: 0.875rem
    fontWeight: '600'
    lineHeight: 1.25rem
  label-sm:
    fontFamily: Hanken Grotesk
    fontSize: 0.75rem
    fontWeight: '600'
    lineHeight: 1rem
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 0.75rem
  margin: 2rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
  space-2xl: 3rem
---

## Brand & Style

This design system drives an archival-grade inventory and commerce platform engineered specifically for antiquarian booksellers, vinyl record curators, and high-value cultural artifacts. The brand marries the disciplined precision of institutional archiving with contemporary operational speed. 

### Visual Style
- **Aesthetic Movement:** Clean Structural Modernism. A focused composition driven by flat, distinct color steps, generous white space or void-like deep purples, and architectural stroke weights.
- **Surface Integrity:** Strictly flat color fills. Absolutely no synthetic gradients, glow fills, or decorative skeuomorphism. Depth is achieved via crisp borders, tinted background surfaces, and elevated surface layers.
- **Iconography:** Monoline SVG icons at 1.5px/2px stroke, geometric terminations, and zero visual ambiguity. Emojis are strictly forbidden across UI, system messages, and metadata fields.
- **Tone:** Methodical, authoritative, tactile-aware, and refined.

## Colors

The palette leverages a stepped monochromatic spectrum across eight designated purple swatches, augmented by crisp functional neutrals to support rapid inventory scanning.

### Core Swatches
- **Dark Amethyst (`#10002B`):** System bedrock. Dark mode foundational background and highest-contrast text in light mode.
- **Dark Amethyst 2 (`#240046`):** Dark mode surface tier 1 (panels, sidebars, base card fills).
- **Indigo Ink (`#3C096C`):** Dark mode active container fill, table hover states, and structural division lines.
- **Indigo Velvet (`#5A189A`):** Low-tier button fills, subtle tag outlines, and interactive hover states.
- **Royal Violet (`#7B2CBF`):** Primary brand accent, selected cell indicators, core call-to-action fills in light mode.
- **Lavender Purple (`#9D4EDD`):** Mid-tone interactive focus rings, active links, and secondary interactive states.
- **Mauve Magic (`#C77DFF`):** Secondary interactive tier, primary action fills in dark mode, and high-attention highlights.
- **Mauve (`#E0AAFF`):** High-readability labels on deep amethyst, chip fills, and subtle focus glows.

### Mode Mapping
- **Light Mode:** Canvas is pure paper (`#FFFFFF`) with panels rendered in soft cold slate (`#F8F7FC`). Outlines use deep violet alpha channels (`rgba(60, 9, 108, 0.12)`). Typography sits on `#10002B` and `#3C096C`. Buttons and tags utilize `#7B2CBF` and `#C77DFF`.
- **Dark Mode:** Canvas is `#10002B`. Sidebars and main surfaces occupy `#240046`. Borders use sharp lines of `#3C096C` or `#5A189A`. Primary actions highlight in `#C77DFF` with contrasting dark text `#10002B`.

### Semantic Status Palette (Flat, High Contrast)
- **In Stock / Matched:** `#06D6A0` (Emerald flat)
- **Low Stock / Discrepancy:** `#FFB703` (Ochre flat)
- **Out of Stock / Archival Alert:** `#E63946` (Crimson flat)
- **Branch Transferred / Processing:** `#4CC9F0` (Cyan flat)

## Typography

The typographic hierarchy combines clean geometry, editorial warmth, and compact data density:

- **Quicksand (Headings & Metric Numbers):** Delivers approachable, modern, architectural headings without aggression. Used on main page titles, high-level metrics, and drawer titles.
- **Newsreader (Editorial & Title Accent):** Replaces generic styling for rare book editions, vinyl pressing pressings, album sleeve descriptions, and artist attributions to impart curated provenance.
- **Hanken Grotesk (General Body UI):** Clean, neutral sans-serif ensuring functional efficiency in administrative layouts.
- **Roboto Flex (Condensed / Tabular Data):** Handles SKUs, barcodes, matrix runout etchings, press serials, stock quantities, and audit log timestamps. Always render with `font-variant-numeric: tabular-nums` to maintain vertical decimal alignment.

## Layout & Spacing

A disciplined 12-column layout built for high information density dashboards and seamless customer-facing product catalogs:

- **Desktop (1200px+):** Fixed 280px master navigation sidebar (collapsible to 72px icon rail), 12-column fluid data canvas with 24px (`space-lg`) gutters, and an optional 380px contextual inspector drawer for branch breakdowns and item conditions.
- **Tablet (768px - 1199px):** 8-column grid with 16px (`space-md`) gutters, collapsible off-canvas navigation drawer, stacked audit trails.
- **Mobile (< 768px):** 4-column layout, sticky bottom operational bar for scanning & quick stock adjustments, edge margins locked at 16px (`space-md`). Tables gracefully transform to split-row cards.

## Elevation & Depth

No shadows or soft gradients are permitted. Visual separation and spatial depth are achieved entirely through **Tonal Layering** and **Crisp Borders**:

- **Layer 0 (Canvas Base):** Dark mode `#10002B` / Light mode `#FFFFFF`.
- **Layer 1 (Card/Table Containers):** Dark mode `#240046` with a solid 1px border of `#3C096C`. Light mode `#F8F7FC` with a 1px border of `rgba(60, 9, 108, 0.12)`.
- **Layer 2 (Popovers, Tooltips, Active Modals):** Dark mode `#3C096C` framed by a 1.5px `#7B2CBF` edge. Light mode `#FFFFFF` bound by a 1.5px `#7B2CBF` border.
- **Interactive Surfaces:** Buttons, dropdown rows, and table lines trigger hard background shifts (e.g., `#240046` transitions directly to `#3C096C` on hover) instead of elevation shadows.

## Shapes

The design system maintains a refined balance between fluid curvature and industrial geometry:
- **Base Elements (Buttons, Inputs, Badges):** 0.5rem (`roundedness: 2`) border-radius.
- **Containers & Tables (rounded-xl / rounded-2xl):** Inventory tables, product grids, and sidebar panels use an expanded curve of `1rem` (`rounded-xl`) or `1.5rem` (`rounded-2xl`).
- **Data Table Outer Boundaries:** All table containers must carry an outer `rounded-2xl` border with `overflow: hidden`, ensuring the first and last rows follow the perimeter curve cleanly.

## Components

### Buttons
- **Primary:** Solid flat `#C77DFF` with `#10002B` bold text in dark mode; `#7B2CBF` with `#FFFFFF` text in light mode. Border radius 0.5rem. Hover state triggers a direct color flip to `#E0AAFF` or `#5A189A`.
- **Secondary / Ghost:** Transparent background with 1px solid `#5A189A` border in dark mode; `#7B2CBF` in light mode. Hover state renders background `#3C096C` or `#F8F7FC`.
- **Destructive:** Flat `#E63946` fill with white text, no shadows.

### Tables & Inventory Data Lists
- **Structure:** Encased in a `rounded-2xl` shell with an explicit 1px outline. Outer header row uses `#240046` (dark) or `#F1EFF9` (light).
- **Row Styling:** Subtle 1px bottom border using `#3C096C` or `#EAE6F5`. Alternating zebra rows are avoided in favor of crisp horizontal borders and deliberate hover highlights (`#3C096C` at 50% opacity).
- **Tabular Cells:** Barcodes, release dates, catalog identifiers, and stock tallies strictly utilize the `tabular-data` token (`Roboto Flex`).

### Badges & Status Indicators
- **Stock Indicators:** Flat pills (`rounded-full`) with a 0.25rem vertical by 0.75rem horizontal padding.
  - *In Stock:* `#06D6A0` text over a 15% opacity tint of the same color, no borders.
  - *Archival Alert / Depleted:* `#E63946` text over a 15% opacity `#E63946` background.
- **Multi-Branch Indicator:** A composite badge featuring a micro mono-line icon (`building-store`), branch acronym (e.g., `NYC-ARCHIVE`), and quantity. Bordered with a flat 1px `#5A189A` line.

### Form Inputs & Selectors
- **Input Fields:** Flat background `#10002B` (dark) or `#FFFFFF` (light), bordered by 1px `#3C096C`. Upon focus, border shifts instantly to 2px solid `#7B2CBF` with zero blur or outer glow.
- **Labels:** Set in `label-sm` (`Hanken Grotesk`), uppercase with positive tracking, placed directly above the input box.

### Audit Log & History Panels
- **Log Feeds:** Continuous vertical track delineated by a 1px solid `#3C096C` line. Nodes are indicated with a 8px flat circular point (`#C77DFF`).
- **Entry Structure:** Split inline arrangement: timestamp in `tabular-data` on the left, editor/curator ID in `label-code`, followed by the action descriptor (e.g., *Updated condition grade to VG+*).