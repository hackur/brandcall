/**
 * Font Combinations for BrandCall
 * 
 * Each combo has a heading font (display) and body font (text).
 * Google Fonts URLs are generated automatically.
 */

export interface FontCombo {
    id: string;
    name: string;
    description: string;
    headingFont: string;
    headingWeight: string;
    bodyFont: string;
    bodyWeight: string;
    googleFontsUrl: string;
}

export const fontCombos: FontCombo[] = [
    {
        id: 'inter-mono',
        name: 'Inter + Inter',
        description: 'Clean and modern, single font family',
        headingFont: 'Inter',
        headingWeight: '600;700;800',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
    },
    {
        id: 'poppins-inter',
        name: 'Poppins + Inter',
        description: 'Geometric headings with readable body text',
        headingFont: 'Poppins',
        headingWeight: '600;700;800',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap',
    },
    {
        id: 'plus-jakarta',
        name: 'Plus Jakarta Sans',
        description: 'Modern SaaS favorite, excellent readability',
        headingFont: 'Plus Jakarta Sans',
        headingWeight: '600;700;800',
        bodyFont: 'Plus Jakarta Sans',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
    },
    {
        id: 'space-grotesk-dm',
        name: 'Space Grotesk + DM Sans',
        description: 'Tech-forward with geometric flair',
        headingFont: 'Space Grotesk',
        headingWeight: '500;600;700',
        bodyFont: 'DM Sans',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=DM+Sans:wght@400;500;600&display=swap',
    },
    {
        id: 'outfit-inter',
        name: 'Outfit + Inter',
        description: 'Friendly geometric headers, professional body',
        headingFont: 'Outfit',
        headingWeight: '500;600;700;800',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
    },
    {
        id: 'manrope-inter',
        name: 'Manrope + Inter',
        description: 'Slightly condensed, modern startup feel',
        headingFont: 'Manrope',
        headingWeight: '600;700;800',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap',
    },
    {
        id: 'sora-inter',
        name: 'Sora + Inter',
        description: 'Geometric and distinctive headers',
        headingFont: 'Sora',
        headingWeight: '500;600;700',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
    },
    {
        id: 'cabinet-grotesk',
        name: 'Cabinet Grotesk + Inter',
        description: 'Bold, distinctive premium feel (Fontshare)',
        headingFont: 'Cabinet Grotesk',
        headingWeight: '700;800',
        bodyFont: 'Inter',
        bodyWeight: '400;500;600',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap',
        // Note: Cabinet Grotesk needs Fontshare CSS
    },
];

export const defaultFontCombo = 'inter-mono';

export function getFontCombo(id: string): FontCombo {
    return fontCombos.find(f => f.id === id) || fontCombos[0];
}

export function getFontFamilyCSS(combo: FontCombo): { heading: string; body: string } {
    const fallback = 'ui-sans-serif, system-ui, sans-serif';
    return {
        heading: `"${combo.headingFont}", ${fallback}`,
        body: `"${combo.bodyFont}", ${fallback}`,
    };
}
