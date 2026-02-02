# BrandCall Brand Identity & Design System

**Version:** 1.0  
**Last Updated:** February 2026

---

## Table of Contents

1. [Brand Essence](#1-brand-essence)
2. [Color Palette](#2-color-palette)
3. [Typography](#3-typography)
4. [Vertical Rhythm](#4-vertical-rhythm)
5. [Spacing System](#5-spacing-system)
6. [Components](#6-components)
7. [Implementation](#7-implementation)

---

## 1. Brand Essence

### Mission
Empower businesses to build trust with every call through branded caller identification.

### Brand Personality
- **Professional** — Enterprise-ready, compliance-focused
- **Trustworthy** — Transparent, secure, reliable
- **Modern** — Clean, contemporary, tech-forward
- **Confident** — Bold but not aggressive

### Voice & Tone
- Clear and direct
- Technical when needed, approachable always
- Authoritative on compliance matters
- No jargon for jargon's sake

---

## 2. Color Palette

### Primary Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| **Indigo 600** (Primary) | `#4F46E5` | 79, 70, 229 | Primary actions, links, focus states |
| **Indigo 700** | `#4338CA` | 67, 56, 202 | Primary hover states |
| **Indigo 500** | `#6366F1` | 99, 102, 241 | Primary light variant |

### Secondary Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| **Violet 600** | `#7C3AED` | 124, 58, 237 | Accents, gradients |
| **Purple 600** | `#9333EA` | 147, 51, 234 | Gradient endpoints |

### Neutral Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| **Slate 950** | `#020617` | 2, 6, 23 | Darkest background |
| **Slate 900** | `#0F172A` | 15, 23, 42 | Dark background |
| **Slate 800** | `#1E293B` | 30, 41, 59 | Card backgrounds (dark) |
| **Slate 700** | `#334155` | 51, 65, 85 | Borders (dark mode) |
| **Slate 600** | `#475569` | 71, 85, 105 | Muted text (dark) |
| **Slate 400** | `#94A3B8` | 148, 163, 184 | Secondary text (dark) |
| **Slate 300** | `#CBD5E1` | 203, 213, 225 | Primary text (dark) |
| **Slate 200** | `#E2E8F0` | 226, 232, 240 | Headings (dark) |
| **Slate 100** | `#F1F5F9` | 241, 245, 249 | Light background |
| **Slate 50** | `#F8FAFC` | 248, 250, 252 | Lightest background |
| **White** | `#FFFFFF` | 255, 255, 255 | Primary text (dark), backgrounds |

### Semantic Colors

| Name | Hex | Usage |
|------|-----|-------|
| **Success** | `#22C55E` (Green 500) | Success states, confirmations |
| **Warning** | `#F59E0B` (Amber 500) | Warnings, cautions |
| **Error** | `#EF4444` (Red 500) | Errors, destructive actions |
| **Info** | `#3B82F6` (Blue 500) | Informational messages |

### Gradients

```css
/* Primary gradient - buttons, CTAs */
--gradient-primary: linear-gradient(135deg, #6366F1 0%, #4F46E5 50%, #4338CA 100%);

/* Hero gradient - backgrounds */
--gradient-hero: linear-gradient(135deg, #020617 0%, #0F172A 50%, #020617 100%);

/* Accent gradient - highlights */
--gradient-accent: linear-gradient(135deg, #9333EA 0%, #6366F1 100%);

/* Glass effect */
--gradient-glass: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
```

---

## 3. Typography

### Font Stack

**Primary Font:** Inter  
**Fallback:** system-ui, -apple-system, sans-serif

```css
--font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

**Why Inter:**
- Excellent screen legibility
- Wide range of weights
- Tabular figures for data
- Free and open source

### Type Scale (1.25 ratio - Major Third)

Based on a 16px base with 1.25 (major third) ratio for harmonious scaling.

| Name | Size | Line Height | Weight | Usage |
|------|------|-------------|--------|-------|
| **Display** | 4.5rem (72px) | 1.1 | 800 | Hero headlines |
| **H1** | 3rem (48px) | 1.2 | 700 | Page titles |
| **H2** | 2.25rem (36px) | 1.25 | 700 | Section headers |
| **H3** | 1.5rem (24px) | 1.3 | 600 | Subsection headers |
| **H4** | 1.25rem (20px) | 1.4 | 600 | Card titles |
| **H5** | 1.125rem (18px) | 1.4 | 600 | Small headers |
| **Body Large** | 1.125rem (18px) | 1.6 | 400 | Lead paragraphs |
| **Body** | 1rem (16px) | 1.6 | 400 | Default body text |
| **Body Small** | 0.875rem (14px) | 1.5 | 400 | Secondary text, captions |
| **Caption** | 0.75rem (12px) | 1.5 | 500 | Labels, hints |
| **Overline** | 0.75rem (12px) | 1.5 | 600 | Eyebrows, categories |

### Font Weights

| Weight | Name | Usage |
|--------|------|-------|
| 400 | Regular | Body text |
| 500 | Medium | Emphasis, UI labels |
| 600 | Semibold | Subheadings, buttons |
| 700 | Bold | Headings |
| 800 | Extrabold | Display text, hero |

### Letter Spacing

| Type | Tracking | CSS |
|------|----------|-----|
| Display/Headlines | -0.025em | `letter-spacing: -0.025em` |
| Subheadings | -0.015em | `letter-spacing: -0.015em` |
| Body | 0 | `letter-spacing: 0` |
| Overline/Caps | 0.05em | `letter-spacing: 0.05em` |

---

## 4. Vertical Rhythm

### Base Unit
**8px** — All spacing, padding, margins, and line heights are multiples of 8px.

### Line Height Ratios
- **Headings:** 1.2 - 1.3 (tight)
- **Body:** 1.5 - 1.6 (comfortable reading)
- **UI Elements:** 1.4 (compact but legible)

### Paragraph Spacing
- **Between paragraphs:** 1.5rem (24px)
- **After headings:** 1rem (16px)
- **Before headings:** 2rem (32px)

### Vertical Rhythm Formula
```
Element Height = (Font Size × Line Height) + Margin
Result should align to 8px grid
```

### Section Spacing

| Context | Spacing |
|---------|---------|
| Between major sections | 6rem (96px) |
| Between subsections | 4rem (64px) |
| Between content blocks | 2rem (32px) |
| Between related items | 1rem (16px) |
| Tight grouping | 0.5rem (8px) |

---

## 5. Spacing System

Based on 8px grid with Tailwind-compatible naming.

| Token | Value | Tailwind | Usage |
|-------|-------|----------|-------|
| `space-1` | 4px | `p-1` | Tight internal spacing |
| `space-2` | 8px | `p-2` | Default internal spacing |
| `space-3` | 12px | `p-3` | Comfortable internal |
| `space-4` | 16px | `p-4` | Standard padding |
| `space-5` | 20px | `p-5` | Generous padding |
| `space-6` | 24px | `p-6` | Card padding |
| `space-8` | 32px | `p-8` | Section padding |
| `space-10` | 40px | `p-10` | Large sections |
| `space-12` | 48px | `p-12` | Hero padding |
| `space-16` | 64px | `p-16` | Major sections |
| `space-20` | 80px | `p-20` | Page sections |
| `space-24` | 96px | `p-24` | Hero sections |

### Container Widths

| Name | Max Width | Usage |
|------|-----------|-------|
| `prose` | 65ch | Long-form content |
| `narrow` | 640px | Forms, dialogs |
| `default` | 1024px | Standard content |
| `wide` | 1280px | Dashboard layouts |
| `full` | 1536px | Full-width sections |

---

## 6. Components

### Buttons

**Primary Button**
```css
/* Base */
background: var(--gradient-primary);
color: white;
font-weight: 600;
padding: 0.75rem 1.5rem;
border-radius: 0.5rem;
box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.25);

/* Hover */
box-shadow: 0 8px 12px -2px rgba(99, 102, 241, 0.35);
transform: translateY(-1px);

/* Active */
transform: translateY(0);
```

**Secondary Button**
```css
background: transparent;
color: var(--slate-300);
border: 1px solid var(--slate-700);
font-weight: 500;
padding: 0.75rem 1.5rem;
border-radius: 0.5rem;

/* Hover */
background: var(--slate-800);
border-color: var(--slate-600);
```

### Cards

```css
background: rgba(30, 41, 59, 0.5); /* slate-800 with transparency */
backdrop-filter: blur(12px);
border: 1px solid rgba(51, 65, 85, 0.5); /* slate-700 */
border-radius: 1rem;
padding: 1.5rem;
```

### Form Inputs

```css
background: var(--slate-800);
border: 1px solid var(--slate-700);
border-radius: 0.5rem;
padding: 0.75rem 1rem;
color: white;
font-size: 1rem;
line-height: 1.5;

/* Focus */
border-color: var(--indigo-500);
outline: none;
box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
```

---

## 7. Implementation

### CSS Custom Properties

```css
:root {
  /* Colors */
  --color-primary: #4F46E5;
  --color-primary-light: #6366F1;
  --color-primary-dark: #4338CA;
  
  /* Typography */
  --font-sans: 'Inter', system-ui, sans-serif;
  --text-base: 1rem;
  --leading-normal: 1.6;
  --leading-tight: 1.25;
  
  /* Spacing */
  --space-unit: 0.5rem;
  
  /* Transitions */
  --transition-fast: 150ms ease;
  --transition-base: 200ms ease;
  --transition-slow: 300ms ease;
}
```

### Tailwind Configuration

See `tailwind.config.js` for full implementation with:
- Extended color palette
- Custom font sizes with line heights
- Spacing scale
- Animation utilities

### Component Library

All reusable components in `/resources/js/Components/` should follow this design system.

---

## Quick Reference

### Do's
- ✅ Use the 8px grid for all spacing
- ✅ Maintain vertical rhythm with consistent line heights
- ✅ Use semantic colors for their intended purpose
- ✅ Apply Inter font with proper weights
- ✅ Use gradients sparingly for emphasis

### Don'ts
- ❌ Don't use arbitrary spacing values
- ❌ Don't mix different type scales
- ❌ Don't use colors outside the palette
- ❌ Don't override line heights without calculation
- ❌ Don't use more than 2 font weights per component
