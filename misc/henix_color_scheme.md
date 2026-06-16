# Henix Color System

Henix character color scheme for UI design.

## The 60/30/10 Rule Application

This design system is strictly structured around the 60-30-10 rule for visual hierarchy and color balance:

- **60% Dominant (Base Canvas)**: Overall canvas tone.
  - *Variables*: `--bg-base` (page background).
- **30% Secondary (Structure & Content)**: Layout organization, typography, and card containers.
  - *Variables*: `--bg-surface`, `--bg-elevated`, `--border`, `--text-primary`, `--text-secondary`, `--text-muted`.
- **10% Accent (High-Contrast Focus)**: Actions, interactive states, highlights, and CTAs.
  - *Variables*: `--accent`, `--accent-hover`, `--accent-volt`, `--border-accent`, `--bg-accent`.

---

## Base Palette

| Color | Hex | Name | Usage |
| :--- | :--- | :--- | :--- |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#01C950; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#01C950` | `henix-neon` | **10% Accent** - Primary accent, CTA, brand mark |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#F5E332; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#F5E332` | `henix-volt` | **10% Accent** - Energy accent, alerts, highlights |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#121212; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#121212` | `henix-obsidian` | **30% Secondary** - Outfit black, dark bg base |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#787878; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#787878` | `henix-iron` | **30% Secondary** - Neutral mid |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#F2CFA0; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#F2CFA0` | `henix-sand` | **10% Accent** - Warm tint accent (skin tone) |

## Extended Ramps

| Color | Hex | Name | Usage |
| :--- | :--- | :--- | :--- |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#02E05A; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#02E05A` | `henix-lime` | **10% Accent** - Hover state for primary |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#009E3D; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#009E3D` | `henix-forest` | **10% Accent** - Active state / deep green accent |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#1E1E1E; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#1E1E1E` | `henix-pine` | **30% Secondary** - Dark surface background |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#D4F2C8; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#D4F2C8` | `henix-leaf` | **30% Secondary** - Light tint container bg |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#262626; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#262626` | `henix-carbon` | **30% Secondary** - Card surface (dark mode) |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#B0B0B0; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#B0B0B0` | `henix-pewter` | **30% Secondary** - Muted text/icon fill |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#F5FAEF; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#F5FAEF;` | `henix-fog` | **60% Dominant** - Page bg (light mode) |
| <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#FFFFFF; border:1px solid rgba(0,0,0,0.1); margin-right:4px; vertical-align:middle;"></span> | `#FFFFFF` | `henix-white` | **30% Secondary** - White surface canvas |

---

## CSS Custom Properties

```css
:root {
  /* 60% DOMINANT (Light Mode Canvas) */
  --bg-base: #F5FAEF;

  /* 30% SECONDARY (Light Mode Structure/Content) */
  --bg-surface: #FFFFFF;
  --bg-elevated: #EDFAE5;
  --text-primary: #0D2E0A;
  --text-secondary: #4A7A38;
  --text-muted: #8AAE78;
  --border: #D0EAC4;

  /* 10% ACCENT (Light Mode Actions/Highlights) */
  --bg-accent: #D4F2C8;
  --accent: #01C950;
  --accent-hover: #02E05A;
  --accent-volt: #D4B800;
  --border-accent: #A0D890;
}

@media (prefers-color-scheme: dark) {
  :root {
    /* 60% DOMINANT (Dark Mode Canvas) */
    --bg-base: #0A0A0A;

    /* 30% SECONDARY (Dark Mode Structure/Content) */
    --bg-surface: #121212;
    --bg-elevated: #1E1E1E;
    --text-primary: #E8F5E4;
    --text-secondary: #6A9E58;
    --text-muted: #3A5A30;
    --border: #262626;

    /* 10% ACCENT (Dark Mode Actions/Highlights) */
    --bg-accent: #262626;
    --accent: #01C950;
    --accent-hover: #02E05A;
    --accent-volt: #F5E332;
    --border-accent: #009E3D;
  }
}
```
