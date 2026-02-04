# BrandCall Brand Guidelines

> Design standards for consistency across all BrandCall interfaces.

---

## Brand Identity

### Mission
Help businesses build trust through verified, branded caller ID.

### Voice
- **Professional** but approachable
- **Confident** without arrogance
- **Clear** - no jargon without explanation
- **Trustworthy** - we handle business communications

---

## Color Palette

### Primary Colors
| Name | Hex | Usage |
|------|-----|-------|
| Brand 600 | `#4F46E5` | Primary buttons, links, accents |
| Brand 500 | `#6366F1` | Hover states, secondary accents |
| Brand 400 | `#818CF8` | Light accents, gradient endpoints |

### Neutral Colors
| Name | Hex | Usage |
|------|-----|-------|
| Slate 950 | `#020617` | Page backgrounds |
| Slate 900 | `#0F172A` | Card backgrounds |
| Slate 800 | `#1E293B` | Elevated surfaces |
| Slate 700 | `#334155` | Borders, dividers |
| Slate 400 | `#94A3B8` | Body text |
| Slate 300 | `#CBD5E1` | Emphasized body text |
| White | `#FFFFFF` | Headings, high emphasis |

### Semantic Colors
| Name | Hex | Usage |
|------|-----|-------|
| Success | `#22C55E` | Success states, confirmations |
| Warning | `#F59E0B` | Warnings, caution states |
| Error | `#EF4444` | Errors, destructive actions |
| Info | `#3B82F6` | Informational messages |

---

## Typography

### Font Family
- **Primary**: Inter
- **Fallback**: system-ui, -apple-system, sans-serif

### Type Scale (1.25 ratio - Major Third)
| Name | Size | Line Height | Letter Spacing | Usage |
|------|------|-------------|----------------|-------|
| Display | 4.5rem (72px) | 1.1 | -0.025em | Hero headlines only |
| H1 | 3rem (48px) | 1.15 | -0.025em | Page titles |
| H2 | 2.25rem (36px) | 1.2 | -0.02em | Section headers |
| H3 | 1.5rem (24px) | 1.3 | -0.015em | Card titles, subsections |
| H4 | 1.25rem (20px) | 1.4 | -0.015em | Small headers |
| Body | 1rem (16px) | 1.6 | 0 | Paragraph text |
| Small | 0.875rem (14px) | 1.5 | 0 | Helper text, captions |
| Tiny | 0.75rem (12px) | 1.5 | 0 | Labels, overlines |

### Font Weights
- **Regular (400)**: Body text
- **Medium (500)**: Labels, navigation
- **Semibold (600)**: Subheadings, buttons
- **Bold (700)**: Headings
- **Extrabold (800)**: Display text only

---

## Spacing

### Base Unit: 8px

All spacing should be multiples of 8px for visual consistency.

| Token | Value | Usage |
|-------|-------|-------|
| space-1 | 4px | Tight spacing (icons with text) |
| space-2 | 8px | Default component padding |
| space-3 | 12px | Small gaps |
| space-4 | 16px | Component gaps |
| space-6 | 24px | Section inner padding |
| space-8 | 32px | Card padding |
| space-12 | 48px | Section gaps |
| space-16 | 64px | Large section gaps |
| space-20 | 80px | Section padding (mobile) |
| space-24 | 96px | Section padding (desktop) |

### Vertical Rhythm
- Body text: 24px line height (1.5 × 16px)
- Paragraphs: 24px margin bottom
- Headings: margin-top = 2× size, margin-bottom = 0.5× size

---

## Components

### Buttons

#### Primary Button
- Background: `bg-gradient-brand` (135deg, #6366F1 → #4F46E5 → #4338CA)
- Text: White, semibold
- Padding: 12px 24px (default), 16px 32px (large)
- Border radius: 8px
- Shadow: `0 4px 14px -3px rgba(99, 102, 241, 0.25)`
- Hover: Lift -2px, increase shadow
- Active: Return to baseline

#### Secondary Button
- Background: Transparent
- Border: 1px slate-700
- Text: slate-300, semibold
- Hover: bg-slate-800, border-slate-600, text-white

#### Ghost Button
- Background: Transparent
- Text: slate-400
- Hover: bg-slate-800, text-white

### Cards
- Background: `slate-800/50` with `backdrop-blur-xl`
- Border: 1px `slate-700/50`
- Border radius: 16px
- Padding: 24px
- Hover (interactive): lift -4px, lighten background

### Inputs
- Background: slate-800
- Border: 1px slate-700
- Border radius: 8px
- Padding: 12px 16px
- Focus: border-brand-500, ring-2 brand-500/20

---

## Layout

### Container Widths
| Name | Max Width | Usage |
|------|-----------|-------|
| Narrow | 672px | Forms, focused content |
| Default | 1024px | Standard pages |
| Wide | 1280px | Marketing pages, dashboards |

### Breakpoints
| Name | Min Width | Usage |
|------|-----------|-------|
| xs | 320px | Small phones (iPhone SE) |
| sm | 640px | Large phones |
| md | 768px | Tablets |
| lg | 1024px | Small laptops |
| xl | 1280px | Desktops |
| 2xl | 1536px | Large screens |

### Grid
- 12-column grid for complex layouts
- 4-column grid for feature cards (2-col on tablet, 1-col on mobile)
- 8px gutter minimum

---

## Responsive Design

### Mobile-First Approach
Always design for mobile first, then enhance for larger screens.

```css
/* Mobile styles (default) */
.component { ... }

/* Tablet and up */
@media (min-width: 768px) { ... }

/* Desktop */
@media (min-width: 1024px) { ... }
```

### Typography Scaling
Type sizes adjust for readability on smaller screens:

| Element | Desktop | Tablet (md) | Mobile (<md) |
|---------|---------|-------------|--------------|
| Display | 4.5rem (72px) | 3.5rem (56px) | 2.5rem (40px) |
| H1 | 3rem (48px) | 2.5rem (40px) | 2rem (32px) |
| H2 | 2.25rem (36px) | 2rem (32px) | 1.75rem (28px) |
| H3 | 1.5rem (24px) | 1.375rem (22px) | 1.25rem (20px) |
| Body | 1rem (16px) | 1rem (16px) | 1rem (16px) |

### Spacing Adjustments
Section spacing reduces on mobile to preserve content visibility:

| Section Padding | Desktop | Tablet | Mobile |
|-----------------|---------|--------|--------|
| Hero | 96px top/bottom | 64px | 48px |
| Content sections | 80px top/bottom | 64px | 48px |
| Card padding | 32px | 24px | 20px |

### Touch Targets
All interactive elements must meet minimum touch target sizes:

- **Minimum size**: 44×44px (Apple HIG)
- **Recommended**: 48×48px
- **Spacing between targets**: 8px minimum

```css
/* Touch-friendly button */
.button {
  min-height: 44px;
  padding: 12px 24px;
}
```

### Navigation

#### Mobile (< 768px)
- Hamburger menu icon (right side of header)
- Full-screen overlay menu or slide-in drawer
- Large touch targets for menu items (48px height minimum)
- Current: condensed header with "Get Started" CTA button only

#### Tablet (768px - 1023px)
- Horizontal navigation with reduced spacing
- May hide secondary nav items

#### Desktop (≥ 1024px)
- Full horizontal navigation
- All nav items visible

### Component Stacking

#### Feature Cards
| Viewport | Layout |
|----------|--------|
| Desktop (xl+) | 4 columns |
| Large tablet (lg) | 3 columns |
| Tablet (md) | 2 columns |
| Mobile (<md) | 1 column (stacked) |

#### Steps/Process
| Viewport | Layout |
|----------|--------|
| Desktop | Horizontal or 2×2 grid |
| Tablet | 2×2 grid |
| Mobile | Vertical stack |

#### CTA Buttons (side-by-side)
| Viewport | Layout |
|----------|--------|
| Desktop | Side-by-side with gap |
| Mobile (<sm) | Stacked vertically, full width |

### Images & Media

#### Hero Images
- Desktop: Full-width or contained
- Mobile: Scale proportionally, consider art direction (cropping)
- Use `srcset` for different resolutions

#### Logos
- Scale logos proportionally
- Minimum width: 80px
- Maximum on mobile: 120px

### Form Inputs on Mobile
- Full-width inputs (100%)
- Minimum height: 48px
- Font size: 16px minimum (prevents iOS zoom on focus)
- Stack labels above inputs (not inline)

```css
input, select, textarea {
  width: 100%;
  min-height: 48px;
  font-size: 16px; /* Prevents iOS zoom */
}
```

### Testing Checklist

#### Required Viewport Tests
- [ ] 320px (iPhone SE, small phones)
- [ ] 375px (iPhone 12/13/14)
- [ ] 414px (iPhone Plus models)
- [ ] 768px (iPad portrait)
- [ ] 1024px (iPad landscape, small laptops)
- [ ] 1280px+ (Desktop)

#### Common Issues to Check
- [ ] Text overflow / truncation
- [ ] Horizontal scrolling (should never happen)
- [ ] Touch targets too small
- [ ] Images not scaling
- [ ] Buttons stacking properly
- [ ] Forms usable on mobile
- [ ] Navigation accessible

---

## Animation Guidelines

### Principles
1. **Purposeful** - Animation should guide attention or provide feedback
2. **Subtle** - Avoid distracting motion
3. **Fast** - Keep durations short (150-300ms for UI, 500ms max for reveals)
4. **Consistent** - Use the same easings throughout

### Timing Functions
- **ease-out**: `cubic-bezier(0.33, 1, 0.68, 1)` - Entering elements
- **ease-in**: `cubic-bezier(0.32, 0, 0.67, 0)` - Exiting elements
- **ease-in-out**: `cubic-bezier(0.65, 0, 0.35, 1)` - Morphing/state changes

### Durations
| Type | Duration | Usage |
|------|----------|-------|
| Instant | 100ms | Hover states, focus |
| Fast | 150ms | Tooltips, small reveals |
| Normal | 200ms | Most UI transitions |
| Slow | 300ms | Page transitions, modals |
| Decorative | 500ms+ | Hero animations (use sparingly) |

### What NOT to Animate
- Large background blobs (distracting)
- Multiple simultaneous animations
- Continuous looping animations (except loading states)
- Text content changes (use opacity transitions instead)

---

## Iconography

### Style
- Outline style (stroke width 2px)
- 24×24px default size
- Current color inheritance

### Icon Library
Use Heroicons (outline variant) for consistency.

```tsx
import { PhoneIcon, ShieldCheckIcon } from '@heroicons/react/24/outline';
```

---

## Imagery

### Photography
- Not currently used - consider adding product screenshots
- If added: high contrast, dark backgrounds preferred

### Illustrations
- Not currently used
- If added: simple, geometric, using brand colors

### Logos
- Display customer logos in grayscale with opacity
- Hover: full color or increased opacity

---

## Accessibility

### Color Contrast
- Text on backgrounds: minimum 4.5:1 ratio
- Large text (24px+): minimum 3:1 ratio
- Interactive elements: minimum 3:1 against adjacent colors

### Focus States
- Always visible focus ring (2px brand-500)
- Focus ring offset for dark backgrounds

### Motion
- Respect `prefers-reduced-motion` media query
- Disable decorative animations for users who prefer reduced motion

---

## Do's and Don'ts

### Do
✅ Use consistent spacing (8px grid)
✅ Maintain vertical rhythm in typography
✅ Keep animations subtle and purposeful
✅ Test on mobile viewports
✅ Use semantic color names

### Don't
❌ Mix different icon styles
❌ Use pure black (#000000) - use slate-950
❌ Add decorative animations without purpose
❌ Break the 8px spacing grid
❌ Use more than 2 font weights in one component

---

*Last updated: 2026-02-04*
