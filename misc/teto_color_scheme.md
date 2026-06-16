# Kasane Teto SV Color System

Kasane Teto SV color scheme for UI design.

## The 60/30/10 Rule Application

This design system is strictly structured around the 60-30-10 rule for visual hierarchy and color balance:

- **60% Dominant (Base Canvas)**: Overall canvas tone.
  - *Variables*: `--bg-base` (page background).
- **30% Secondary (Structure & Content)**: Layout organization, typography, card containers, and secondary fills.
  - *Variables*: `--bg-surface`, `--bg-elevated`, `--border`, `--text-primary`, `--text-secondary`, `--text-muted`, `--steel`.
- **10% Accent (High-Contrast Focus)**: Actions, interactive states, highlights, and CTAs.
  - *Variables*: `--accent`, `--accent-hover`, `--border-accent`, `--bg-accent`.

---

## Base Palette

| Color | Hex | Name | Usage |
| :--- | :--- | :--- | :--- |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#C1272D; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#C1272D` | `teto-crimson` | **10% Accent** - Primary accent, CTA, brand |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#7A7C80; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#7A7C80` | `teto-steel` | **30% Secondary** - Neutral mid, secondary UI |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#D6D4D0; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#D6D4D0` | `teto-ash` | **30% Secondary** - Borders, dividers, light bg |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#1A1A1C; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#1A1A1C` | `teto-coal` | **30% Secondary** - Dark bg, text on light |

## Extended Ramps

| Color | Hex | Name | Usage |
| :--- | :--- | :--- | :--- |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#E8333A; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#E8333A` | `teto-scarlet` | **10% Accent** - Hover state / brighter red |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#8B1A1E; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#8B1A1E` | `teto-burgundy` | **10% Accent** - Active state / deep red |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#3D1518; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#3D1518` | `teto-wine` | **10% Accent** - Dark red surface (dark mode) |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#FAE8E8; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#FAE8E8` | `teto-blush` | **10% Accent** - Light red tint (light mode) |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#B8BAC0; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#B8BAC0` | `teto-silver` | **30% Secondary** - Secondary text, muted icons |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#4A4A50; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#4A4A50` | `teto-smoke` | **30% Secondary** - Dark surface cards |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#F6F3F0; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#F6F3F0` | `teto-ivory` | **60% Dominant** - Page bg (light mode) |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#FFFFFF; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#FFFFFF` | `teto-chalk` | **30% Secondary** - Card bg, surfaces (light) |

---

## CSS Custom Properties

```css
:root {
  /* 60% DOMINANT (Light Mode Canvas) */
  --bg-base: #F6F3F0;

  /* 30% SECONDARY (Light Mode Structure/Content) */
  --bg-surface: #FFFFFF;
  --bg-elevated: #FAFAFA;
  --text-primary: #1A1A1C;
  --text-secondary: #545458;
  --text-muted: #8A8A90;
  --border: #E4E2DF;
  --steel: #7A7C80;

  /* 10% ACCENT (Light Mode Actions/Highlights) */
  --bg-accent: #FAE8E8;
  --accent: #C1272D;
  --accent-hover: #A01E23;
  --border-accent: #F0C0C2;
}

@media (prefers-color-scheme: dark) {
  :root {
    /* 60% DOMINANT (Dark Mode Canvas) */
    --bg-base: #0F0F11;

    /* 30% SECONDARY (Dark Mode Structure/Content) */
    --bg-surface: #1A1A1C;
    --bg-elevated: #242428;
    --text-primary: #F4F0EE;
    --text-secondary: #9A9AA0;
    --text-muted: #5A5A60;
    --border: #2E2E32;
    --steel: #7A7C80;

    /* 10% ACCENT (Dark Mode Actions/Highlights) */
    --bg-accent: #3D1518;
    --accent: #C1272D;
    --accent-hover: #E8333A;
    --border-accent: #5A1A1D;
  }
}
```
