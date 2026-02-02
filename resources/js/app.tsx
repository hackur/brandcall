import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { FontProvider } from '@/contexts/FontContext';
import FontPicker from '@/Components/FontPicker';

const appName = import.meta.env.VITE_APP_NAME || 'BrandCall';

// Show font picker in dev or with ?fonts query param
const showFontPicker = import.meta.env.DEV || 
    (typeof window !== 'undefined' && window.location.search.includes('fonts'));

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <FontProvider>
                <App {...props} />
                {showFontPicker && <FontPicker />}
            </FontProvider>
        );
    },
    progress: {
        color: '#6366F1',
    },
});
