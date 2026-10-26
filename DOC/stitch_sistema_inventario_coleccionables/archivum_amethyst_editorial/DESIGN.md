---
name: Archivum Amethyst Editorial
colors:
  surface: '#fef7ff'
  surface-dim: '#e2d3ff'
  surface-bright: '#fef7ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f9f1ff'
  surface-container: '#f4eaff'
  surface-container-high: '#efe4ff'
  surface-container-highest: '#eaddff'
  on-surface: '#24005b'
  on-surface-variant: '#4a4455'
  inverse-surface: '#391e70'
  inverse-on-surface: '#f6edff'
  outline: '#7b7487'
  outline-variant: '#ccc3d8'
  surface-tint: '#732ee4'
  primary: '#630ed4'
  on-primary: '#ffffff'
  primary-container: '#7c3aed'
  on-primary-container: '#ede0ff'
  inverse-primary: '#d2bbff'
  secondary: '#625595'
  on-secondary: '#ffffff'
  secondary-container: '#c6b7ff'
  on-secondary-container: '#524584'
  tertiary: '#7300c6'
  on-tertiary: '#ffffff'
  tertiary-container: '#8f2de6'
  on-tertiary-container: '#f2dfff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#eaddff'
  primary-fixed-dim: '#d2bbff'
  on-primary-fixed: '#25005a'
  on-primary-fixed-variant: '#5a00c6'
  secondary-fixed: '#e7deff'
  secondary-fixed-dim: '#ccbeff'
  on-secondary-fixed: '#1e0e4e'
  on-secondary-fixed-variant: '#4a3d7c'
  tertiary-fixed: '#f0dbff'
  tertiary-fixed-dim: '#ddb8ff'
  on-tertiary-fixed: '#2c0051'
  on-tertiary-fixed-variant: '#6800b4'
  background: '#fef7ff'
  on-background: '#24005b'
  surface-variant: '#eaddff'
typography:
  display-lg:
    fontFamily: Quicksand
    fontSize: 48px
    fontWeight: '600'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Quicksand
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Quicksand
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Quicksand
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: '0'
  headline-md:
    fontFamily: Quicksand
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
  headline-sm:
    fontFamily: Quicksand
    fontSize: 20px
    fontWeight: '500'
    lineHeight: 28px
  title-md:
    fontFamily: Quicksand
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Quicksand
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Quicksand
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Quicksand
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Barlow Condensed
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.08em
  label-md:
    fontFamily: Barlow Condensed
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.1em
  label-sm:
    fontFamily: Barlow Condensed
    fontSize: 10px
    fontWeight: '500'
    lineHeight: 14px
    letterSpacing: 0.12em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 3rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system embodies curatorial refinement, archival preservation, and tactile luxury. Built for connoisseurs, archivists, and collectors of rare manuscripts, historical vinyl pressings, and authenticated ephemera, the interface evokes the hushed, luminous atmosphere of a climate-controlled private library.

The visual style synthesizes modern editorial layout discipline with ethereal, layered translucency:
- **Atmospheric Luminosity:** Surfaces rely on delicate lilac, alabaster, and dusted lavender washes, avoiding sterile clinical whites.
- **Museum-Grade Contrast:** Deep imperial violet typography replaces standard charcoal or black, ensuring high contrast while maintaining a warm, chromatic depth.
- **Delicate Precision:** Fine hairlines, precise metadata alignments, and generous white space elevate every rare asset into a curated exhibit.
- **Zero Emojis & Bespoke Iconography:** Universal exclusion of informal pictograms; all visual signals are expressed through razor-thin, geometric vector iconography.

## Colors

The palette establishes an illuminated chromatic balance using tiered violet spectrums:

- **Primary Accent (`#7c3aed`, `#8b5cf6`):** Vibrant amethyst reserved for primary interactive vectors, active states, focus halos, and critical curation actions.
- **Secondary Tones (`#c4b5fd`, `#d8b4fe`):** Soft wisteria utilized for architectural boundaries, delicate dividers, inactive tabs, and subtle container strokes.
- **Tertiary Accent (`#9333ea`):** Deep electric amethyst for high-prominence badges, authenticated collection badges, and interactive hover shifts.
- **Neutral Foreground (`#2e1065`, `#3b0764`, `#4c1d95`):** Imperial violet depths. Headings employ `#2e1065` for supreme visual authority; body text utilizes `#3b0764`; secondary provenance metadata uses `#4c1d95`.
- **Surfaces & Canvases (`#f7f2fa`, `#ede7f6`, `#f3e8ff`):** Graded base planes. The canvas grounds on `#f7f2fa`, moving to `#ede7f6` for structural sidebars, and `#f3e8ff` for elevated cards and transient panels.

## Typography

The typographic hierarchy pairs the soft geometric elegance of Quicksand with the condensed architectural rigor of Barlow Condensed (the system's condensed counterpart).

- **Titles & Narrative Content (Quicksand):** Used across display headlines, section headers, long-form provenance essays, and catalog overviews. Quicksand's rounded terminals soften rare artifact presentations, removing archaic rigidity and imparting contemporary boutique luxury.
- **Cataloguing & Meta Metrics (Barlow Condensed):** Applied strictly to tabular indices, archive registration numbers, condition grading tags (e.g., "MINT / UNPLAYED", "FOLIO 1ST ED."), catalog numbers, and timestamps. All condensed label scales mandate uppercase transformation with generous tracking to emulate fine museum placards.

## Layout & Spacing

The spatial architecture balances an open gallery layout with structured inventory grids:

- **Grid Framework:** A 12-column fluid grid on desktop (1440px max content width), collapsing to 6 columns on tablet (768px–1024px) and 2 or 4 columns on mobile viewports (<768px).
- **Asymmetric Canvas:** Archival item galleries utilize alternating column span alignments (e.g., 7-column specimen zoom paired with 5-column ledger provenance) to maintain editorial rhythm.
- **Rhythm Rules:** Vertical cadences prioritize breathing room over density. Sectional transitions require `space-xl` separation, while related descriptive fields maintain `space-sm` or `space-xs` proximity. Outer canvases implement comfortable gutter boundaries to prevent artifacts from colliding with display viewports.

## Elevation & Depth

Visual depth is achieved through ambient violet illumination and tinted translucent layering rather than dense structural drop-shadows:

- **Tonal Layers:** Hierarchies are established by progressing up the surface color ladder: canvas (`#f7f2fa`), structural shelves/panels (`#ede7f6`), active inspection cards (`#f3e8ff`), and hovering flyouts (`#ffffff`).
- **Low-Contrast Wisteria Outlines:** Every card and structural module is bound by a crisp 1px hairline border (`#d8b4fe` at 60% opacity or `#c4b5fd` at 100%), grounding elements cleanly on soft backgrounds.
- **Ambient Violet Halos:** Floating inspection loupes, modal overlays, and popovers cast ultra-soft, diffused shadows tinted with imperial violet:
  - Low (Cards): `0 2px 8px -2px rgba(46, 16, 101, 0.04), 0 1px 3px 0 rgba(124, 58, 237, 0.03)`
  - Medium (Dropdowns/Filters): `0 8px 24px -4px rgba(46, 16, 101, 0.08), 0 2px 6px -1px rgba(124, 58, 237, 0.04)`
  - High (Dialogs): `0 20px 48px -8px rgba(46, 16, 101, 0.14), 0 4px 12px 0 rgba(124, 58, 237, 0.06)`
- **Archival Glass:** Floating sticky navigation bars and catalog status ribbons utilize a semi-transparent lilac backdrop (`rgba(247, 242, 250, 0.85)`) coupled with a `16px` backdrop filter blur.

## Shapes

The design system maintains a balanced, approachable curvature that harmonizes with Quicksand's letterforms:

- **Base Radius (`0.5rem` / `8px`):** Standard for interactive inputs, buttons, filter chips, and image viewports.
- **Extended Radius (`1rem` / `16px`):** Applied to collectible profile cards, document dossiers, media players, and inspection panels.
- **Pill Radius (`9999px`):** Reserved exclusively for status indicators, condition tags, archive category selectors, and playhead progress scrubbers.

## Components

### Buttons
- **Primary:** Filled in `#7c3aed` with text in `#ffffff` (Quicksand 600). Soft hover transition to `#6d28d9`. In active states, a 2px offset focus ring in `#c4b5fd`.
- **Secondary:** Surface filled in `#ede7f6`, text in `#3b0764`, bordered with 1px `#c4b5fd`. Transitions to `#f3e8ff` on hover.
- **Tertiary / Curatorial Ghost:** Transparent background, text in `#4c1d95`, no border. Transitions to `rgba(196, 181, 253, 0.2)` on hover.
- **Dimensions:** 40px height for desktop, 44px for touch targets, with consistent horizontal padding (`space-md`).

### Badges & Condition Chips
- **Condition Tags (Mint, VG+, Archival):** Pill-shaped, background in `#ede7f6`, border 1px `#d8b4fe`. Typography rendered using `label-sm` in Barlow Condensed uppercase in `#2e1065`.
- **Authentication Seals:** Filled in `#9333ea` with `#ffffff` text, accompanied by a precise 12px monochrome vector validation seal icon.

### Form Inputs & Selectors
- **Text Fields & Search:** Background in `#f3e8ff` (or `#ffffff` on `#ede7f6` panels), bordered by 1px `#c4b5fd`. Text in `#2e1065`, placeholder in `#8b5cf6` at 50% opacity.
- **Focus State:** Border shifts dynamically to `#7c3aed` with a soft 3px box-shadow halo: `0 0 0 3px rgba(124, 58, 237, 0.15)`.
- **Checkboxes & Radios:** 18px squares/circles, border 1.5px `#c4b5fd`, background `#f7f2fa`. When checked, background fills with `#7c3aed`, housing an interior SVG check in clean `#ffffff`.

### Archival Cards & Asset Dossiers
- **Structure:** Encased in `#f3e8ff` background, outlined with 1px `#d8b4fe`. Internal content separated into an artwork/specimen frame and a catalog details ledger.
- **Interactive State:** Hover elevates the card slightly (-2px vertical transform) and intensifies the border color to `#8b5cf6`.
- **Media Displays:** Vinyl and manuscript preview containers use `#ede7f6` matte surrounds to frame physical items authentically.

### Specialized Component: Provenance Timeline
- A refined vertical rail in 1px `#c4b5fd`. Milestones marked by 8px `#7c3aed` hollow nodes on `#f7f2fa` backgrounds. Date and ledger stamps rendered in Barlow Condensed `label-md`, accompanied by historical annotations set in Quicksand `body-sm`.