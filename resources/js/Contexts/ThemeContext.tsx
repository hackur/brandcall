import { createContext, useContext, useEffect, useState, ReactNode } from 'react';

type Theme = 'dark' | 'light';

interface ThemeContextType {
    theme: Theme;
    setTheme: (theme: Theme) => void;
    toggleTheme: () => void;
    isDark: boolean;
}

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

const THEME_STORAGE_KEY = 'brandcall-theme';

// Get initial theme synchronously to prevent flash
function getInitialTheme(defaultTheme: Theme): Theme {
    if (typeof window === 'undefined') return defaultTheme;
    
    const savedTheme = localStorage.getItem(THEME_STORAGE_KEY) as Theme | null;
    if (savedTheme === 'dark' || savedTheme === 'light') {
        return savedTheme;
    }
    
    // Default to dark mode (brand preference)
    return defaultTheme;
}

// Apply theme class immediately (before React hydration)
function applyThemeClass(theme: Theme) {
    if (typeof document === 'undefined') return;
    
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
}

export function ThemeProvider({ children, defaultTheme = 'dark' }: { children: ReactNode; defaultTheme?: Theme }) {
    // Initialize synchronously to prevent flash
    const [theme, setThemeState] = useState<Theme>(() => {
        const initial = getInitialTheme(defaultTheme);
        applyThemeClass(initial);
        return initial;
    });

    // Apply theme class whenever it changes
    useEffect(() => {
        applyThemeClass(theme);
        localStorage.setItem(THEME_STORAGE_KEY, theme);
    }, [theme]);

    const setTheme = (newTheme: Theme) => {
        setThemeState(newTheme);
    };

    const toggleTheme = () => {
        setThemeState(prev => prev === 'dark' ? 'light' : 'dark');
    };

    return (
        <ThemeContext.Provider value={{ theme, setTheme, toggleTheme, isDark: theme === 'dark' }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (context === undefined) {
        throw new Error('useTheme must be used within a ThemeProvider');
    }
    return context;
}

// Hook for components that might be outside ThemeProvider
export function useThemeWithFallback() {
    const context = useContext(ThemeContext);
    if (context === undefined) {
        // Fallback for components outside provider
        const [isDark, setIsDark] = useState(true);
        
        useEffect(() => {
            const savedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            setIsDark(savedTheme !== 'light');
        }, []);
        
        return {
            theme: isDark ? 'dark' : 'light' as Theme,
            isDark,
            toggleTheme: () => setIsDark(prev => !prev),
            setTheme: (t: Theme) => setIsDark(t === 'dark'),
        };
    }
    return context;
}
