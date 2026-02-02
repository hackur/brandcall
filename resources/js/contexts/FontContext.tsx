import { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { FontCombo, fontCombos, defaultFontCombo, getFontCombo, getFontFamilyCSS } from '@/config/fonts';

interface FontContextValue {
    currentFont: FontCombo;
    setFont: (id: string) => void;
    fontCombos: FontCombo[];
    cycleFont: () => void;
}

const FontContext = createContext<FontContextValue | undefined>(undefined);

const STORAGE_KEY = 'brandcall-font';

export function FontProvider({ children }: { children: ReactNode }) {
    const [currentFont, setCurrentFont] = useState<FontCombo>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                return getFontCombo(saved);
            }
        }
        return getFontCombo(defaultFontCombo);
    });

    // Load Google Fonts dynamically
    useEffect(() => {
        const linkId = 'google-fonts-dynamic';
        let link = document.getElementById(linkId) as HTMLLinkElement | null;
        
        if (!link) {
            link = document.createElement('link');
            link.id = linkId;
            link.rel = 'stylesheet';
            document.head.appendChild(link);
        }
        
        link.href = currentFont.googleFontsUrl;
    }, [currentFont]);

    // Apply CSS variables for fonts
    useEffect(() => {
        const { heading, body } = getFontFamilyCSS(currentFont);
        document.documentElement.style.setProperty('--font-heading', heading);
        document.documentElement.style.setProperty('--font-body', body);
        
        // Also set on body for default inheritance
        document.body.style.fontFamily = body;
    }, [currentFont]);

    const setFont = (id: string) => {
        const combo = getFontCombo(id);
        setCurrentFont(combo);
        localStorage.setItem(STORAGE_KEY, id);
    };

    const cycleFont = () => {
        const currentIndex = fontCombos.findIndex(f => f.id === currentFont.id);
        const nextIndex = (currentIndex + 1) % fontCombos.length;
        setFont(fontCombos[nextIndex].id);
    };

    return (
        <FontContext.Provider value={{ currentFont, setFont, fontCombos, cycleFont }}>
            {children}
        </FontContext.Provider>
    );
}

export function useFont() {
    const context = useContext(FontContext);
    if (!context) {
        throw new Error('useFont must be used within a FontProvider');
    }
    return context;
}
