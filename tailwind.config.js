import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            // Typography - Inter with system fallbacks
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            // Type Scale (1.25 ratio - Major Third)
            fontSize: {
                'xs': ['0.75rem', { lineHeight: '1.5', letterSpacing: '0' }],
                'sm': ['0.875rem', { lineHeight: '1.5', letterSpacing: '0' }],
                'base': ['1rem', { lineHeight: '1.6', letterSpacing: '0' }],
                'lg': ['1.125rem', { lineHeight: '1.6', letterSpacing: '0' }],
                'xl': ['1.25rem', { lineHeight: '1.4', letterSpacing: '-0.015em' }],
                '2xl': ['1.5rem', { lineHeight: '1.3', letterSpacing: '-0.015em' }],
                '3xl': ['1.875rem', { lineHeight: '1.25', letterSpacing: '-0.02em' }],
                '4xl': ['2.25rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],
                '5xl': ['3rem', { lineHeight: '1.15', letterSpacing: '-0.025em' }],
                '6xl': ['3.75rem', { lineHeight: '1.1', letterSpacing: '-0.025em' }],
                '7xl': ['4.5rem', { lineHeight: '1.1', letterSpacing: '-0.025em' }],
            },

            // Brand Colors
            colors: {
                brand: {
                    50: '#EEF2FF',
                    100: '#E0E7FF',
                    200: '#C7D2FE',
                    300: '#A5B4FC',
                    400: '#818CF8',
                    500: '#6366F1',
                    600: '#4F46E5', // Primary
                    700: '#4338CA',
                    800: '#3730A3',
                    900: '#312E81',
                    950: '#1E1B4B',
                },
            },

            // Spacing - 8px grid based
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
                '30': '7.5rem',
            },

            // Border Radius
            borderRadius: {
                '4xl': '2rem',
            },

            // Box Shadow with brand colors
            boxShadow: {
                'brand': '0 4px 14px -3px rgba(99, 102, 241, 0.25)',
                'brand-lg': '0 10px 25px -5px rgba(99, 102, 241, 0.3)',
                'brand-xl': '0 20px 40px -10px rgba(99, 102, 241, 0.35)',
                'glow': '0 0 20px rgba(99, 102, 241, 0.4)',
                'glow-lg': '0 0 40px rgba(99, 102, 241, 0.3)',
                'inner-brand': 'inset 0 2px 4px rgba(99, 102, 241, 0.1)',
            },

            // Background Images (gradients)
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-brand': 'linear-gradient(135deg, #6366F1 0%, #4F46E5 50%, #4338CA 100%)',
                'gradient-hero': 'linear-gradient(135deg, #020617 0%, #0F172A 50%, #020617 100%)',
                'gradient-accent': 'linear-gradient(135deg, #9333EA 0%, #6366F1 100%)',
                'gradient-glass': 'linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%)',
            },

            // Animations
            animation: {
                'blob': 'blob 15s infinite ease-in-out',
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'pulse-slow': 'pulse 3s ease-in-out infinite',
            },

            keyframes: {
                blob: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '25%': { transform: 'translate(20px, -30px) scale(1.1)' },
                    '50%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '75%': { transform: 'translate(30px, 10px) scale(1.05)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },

            // Transition timing
            transitionDuration: {
                '400': '400ms',
            },

            // Max widths for containers
            maxWidth: {
                'prose': '65ch',
                '8xl': '88rem',
            },
        },
    },

    plugins: [forms],
};
