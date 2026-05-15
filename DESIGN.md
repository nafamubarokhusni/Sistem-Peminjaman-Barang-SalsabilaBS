---
name: Academic Heritage
colors:
  surface: '#f8f9fa'
  surface-dim: '#d9dadb'
  surface-bright: '#f8f9fa'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f5'
  surface-container: '#edeeef'
  surface-container-high: '#e7e8e9'
  surface-container-highest: '#e1e3e4'
  on-surface: '#191c1d'
  on-surface-variant: '#3e4946'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f2'
  outline: '#6e7a76'
  outline-variant: '#bdc9c5'
  surface-tint: '#006b5e'
  primary: '#005e53'
  on-primary: '#ffffff'
  primary-container: '#00796b'
  on-primary-container: '#a1feec'
  inverse-primary: '#7ad7c6'
  secondary: '#795900'
  on-secondary: '#ffffff'
  secondary-container: '#fec330'
  on-secondary-container: '#6f5100'
  tertiary: '#005e57'
  on-tertiary: '#ffffff'
  tertiary-container: '#007970'
  on-tertiary-container: '#9efff3'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#97f3e2'
  primary-fixed-dim: '#7ad7c6'
  on-primary-fixed: '#00201b'
  on-primary-fixed-variant: '#005047'
  secondary-fixed: '#ffdfa0'
  secondary-fixed-dim: '#f8bd2a'
  on-secondary-fixed: '#261a00'
  on-secondary-fixed-variant: '#5c4300'
  tertiary-fixed: '#78f7e9'
  tertiary-fixed-dim: '#59dacd'
  on-tertiary-fixed: '#00201d'
  on-tertiary-fixed-variant: '#00504a'
  background: '#f8f9fa'
  on-background: '#191c1d'
  surface-variant: '#e1e3e4'
  deep-forest: '#004D40'
  surface-white: '#FFFFFF'
  text-primary: '#1A2E2C'
  text-secondary: '#546E7A'
typography:
  headline-xl:
    fontFamily: Hanken Grotesk
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
  headline-md:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Source Sans 3
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Source Sans 3
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Hanken Grotesk
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  caption:
    fontFamily: Source Sans 3
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
  stack-sm: 12px
  stack-md: 24px
  stack-lg: 48px
---

## Brand & Style

The brand identity centers on the intersection of traditional Islamic values and modern academic excellence. The design system prioritizes a **Corporate Modern** aesthetic with high-quality finishes, ensuring the institution feels established, trustworthy, and welcoming to both students and parents. 

The visual narrative is driven by structured layouts, intentional whitespace, and a sophisticated color palette that balances the serenity of deep greens with the optimism of gold. The interface should feel "premium academic"—cleaner than a typical government site but more grounded than a standard tech startup.

## Colors

The palette is anchored by **Deep Teal (#00796B)**, representing growth, stability, and Islamic heritage. This is complemented by **Vibrant Yellow-Gold (#FBC02D)**, used sparingly for calls to action, highlights, and moments of achievement.

- **Primary (Deep Teal):** Used for headers, primary buttons, and core branding elements.
- **Secondary (Yellow-Gold):** Reserved for emphasis, interactive states, and "Premium" academic badges.
- **Tertiary (Bright Teal):** Used for accents, iconography backgrounds, and data visualization.
- **Neutral:** A very light gray-teal foundation maintains a clean, modern atmosphere without the starkness of pure white on large surfaces.

## Typography

The typography strategy utilizes two high-performance sans-serifs to maintain a professional, academic tone. 

**Hanken Grotesk** is chosen for headlines and labels. Its sharp, contemporary geometry provides a sense of precision and forward-thinking. **Source Sans 3** is utilized for body copy, offering exceptional legibility for long-form educational content and administrative details.

Hierarchy is strictly enforced through weight contrast. Headlines should use the Primary Deep Teal color, while body text stays in a dark charcoal-teal for optimal reading comfort.

## Layout & Spacing

The design system employs a **Fixed Grid** approach for desktop screens to maintain a curated, editorial feel. The layout is structured on an 8px base grid.

- **Desktop (1280px+):** 12-column grid with 24px gutters and 48px outer margins.
- **Tablet (768px - 1024px):** 8-column grid with 24px gutters and 32px outer margins.
- **Mobile (<767px):** 4-column grid with 16px gutters and 16px outer margins.

Vertical rhythm should follow the "Stack" units, ensuring consistent breathing room between content blocks. Sections should be clearly separated by significant vertical padding (Stack-LG) to prevent the academic content from feeling cluttered.

## Elevation & Depth

To reflect a "Modern Boarding School" feel, depth is conveyed through **Tonal Layers** supplemented by **Ambient Shadows**.

1.  **Level 0 (Base):** Surface-Neutral (#F8F9FA). Used for the main background.
2.  **Level 1 (Cards):** Surface-White (#FFFFFF) with a very soft, diffused shadow (15% opacity Primary Teal, 20px blur, 4px Y-offset). This is the primary container for content.
3.  **Level 2 (Interactive/Hover):** Increased shadow spread and a subtle 1px stroke in Primary Teal at 10% opacity.
4.  **Level 3 (Navigation/Modals):** High-diffusion shadows to create clear separation from the page content.

Avoid heavy black shadows. All shadows must be tinted with the Deep Teal hue to maintain a cohesive, sophisticated atmosphere.

## Shapes

The shape language is **Rounded**, striking a balance between the rigid structure of traditional academia and the approachability of a boarding school environment.

- **Standard Elements (Buttons, Inputs):** 0.5rem (8px) radius.
- **Large Elements (Cards, Featured Images):** 1rem (16px) radius.
- **Badges/Chips:** Fully rounded (pill-shaped) to distinguish them from actionable buttons.

Iconography should follow this logic, using "broken" or "softened" corners rather than sharp 90-degree angles.

## Components

### Buttons
Primary buttons use the Deep Teal background with White text. Secondary buttons use a Teal outline or the Yellow-Gold for high-priority conversion points like "Enroll Now." All buttons feature a subtle transition to a slightly darker shade on hover.

### Cards
Cards are the foundational component. They must feature white backgrounds, the Level 1 elevation shadow, and a 16px corner radius. Feature cards (e.g., Program Highlights) may include a top-border accent in Yellow-Gold (4px thickness).

### Chips & Tags
Used for category labels (e.g., "Academic," "Tahfidz," "Sports"). They should use a low-opacity background of the Primary or Tertiary colors with high-contrast text.

### Input Fields
Fields should have a 1px stroke in a light neutral color, which shifts to Primary Teal on focus. Use Hanken Grotesk for labels to maintain the professional aesthetic.

### Iconography
Icons should be thin-to-medium stroke weight, utilizing the Primary Teal. Icons within cards should be housed in a soft-rounded Tertiary Teal container at 10% opacity to create a visual focal point.