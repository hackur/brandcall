import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, useState, useEffect } from 'react';
import { PageProps } from '@/types';
import ThemeToggle from '@/Components/ThemeToggle';
import { ToastProvider } from '@/Components/FlashMessages';

// Custom SVG Icons
const Icons = {
    phone: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    menu: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    x: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    arrow: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

const navLinks = [
    { href: '/features', label: 'Features' },
    { href: '/solutions', label: 'Solutions' },
    { href: '/pricing', label: 'Pricing' },
    { href: '/guide/branded-calling', label: 'Guide' },
    { href: '/faq', label: 'FAQ' },
];

const footerLinks = {
    product: [
        { href: '/features', label: 'Features' },
        { href: '/pricing', label: 'Pricing' },
        { href: '/solutions', label: 'Solutions' },
        { href: '/guide/branded-calling', label: 'BCID Guide' },
    ],
    resources: [
        { href: '/what-is-branded-caller-id', label: 'What is Branded Caller ID?' },
        { href: '/stir-shaken-explained', label: 'STIR/SHAKEN Guide' },
        { href: '/spam-check', label: 'Spam Check' },
        { href: '/faq', label: 'FAQ' },
        { href: '/glossary', label: 'Glossary' },
        { href: '/compliance', label: 'Compliance' },
        { href: 'mailto:support@brandcall.io', label: 'Support', external: true },
    ],
    legal: [
        { href: '#', label: 'Privacy Policy' },
        { href: '#', label: 'Terms of Service' },
        { href: '/compliance', label: 'Security' },
        { href: '/compliance', label: 'Compliance' },
    ],
};

interface MarketingLayoutProps extends PropsWithChildren {
    title?: string;
}

export default function MarketingLayout({ children }: MarketingLayoutProps) {
    const { auth } = usePage<PageProps>().props;
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);

    useEffect(() => {
        const handleScroll = () => {
            const currentScrollY = window.scrollY;
            const scrollThreshold = 10;
            
            if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) return;
            
            if (currentScrollY < 50) {
                setIsHeaderVisible(true);
            } else if (currentScrollY > lastScrollY) {
                setIsHeaderVisible(false);
            } else {
                setIsHeaderVisible(true);
            }
            
            setLastScrollY(currentScrollY);
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, [lastScrollY]);

    return (
        <div className="min-h-screen bg-theme-primary">
            <ToastProvider />
            {/* Navigation */}
            <nav 
                className={`fixed top-0 left-0 right-0 z-50 px-5 sm:px-6 py-3 sm:py-4 bg-theme-primary/95 backdrop-blur-lg border-b border-theme-primary transition-all duration-300 ${
                    isHeaderVisible ? 'translate-y-0' : '-translate-y-full'
                }`}
            >
                <div className="max-w-7xl mx-auto flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-2 sm:gap-3">
                        <div className="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-brand-600">
                            <div className="h-4 w-4 sm:h-5 sm:w-5 text-white">
                                {Icons.phone}
                            </div>
                        </div>
                        <span className="text-lg sm:text-xl font-semibold text-theme-primary">BrandCall</span>
                    </Link>

                    {/* Desktop Navigation */}
                    <div className="hidden md:flex items-center gap-8">
                        {navLinks.map((link) => (
                            <Link 
                                key={link.href}
                                href={link.href} 
                                className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors"
                            >
                                {link.label}
                            </Link>
                        ))}
                    </div>

                    <div className="flex items-center gap-2 sm:gap-4">
                        <ThemeToggle />

                        {/* Mobile menu button */}
                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden p-2 text-theme-tertiary hover:text-theme-primary"
                        >
                            <div className="h-5 w-5">
                                {mobileMenuOpen ? Icons.x : Icons.menu}
                            </div>
                        </button>

                        {auth.user ? (
                            <Link 
                                href={route('dashboard')} 
                                className="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="hidden sm:block px-4 py-2 text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors"
                                >
                                    Sign In
                                </Link>
                                <Link 
                                    href={route('register')} 
                                    className="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                >
                                    Get Started
                                </Link>
                            </>
                        )}
                    </div>
                </div>

                {/* Mobile Navigation */}
                {mobileMenuOpen && (
                    <div className="md:hidden mt-4 pb-4 border-t border-theme-primary pt-4">
                        <div className="flex flex-col gap-4">
                            {navLinks.map((link) => (
                                <Link 
                                    key={link.href}
                                    href={link.href} 
                                    className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    {link.label}
                                </Link>
                            ))}
                            <div className="pt-4 border-t border-theme-primary flex flex-col gap-2">
                                {!auth.user && (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="px-4 py-2 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md text-center"
                                        >
                                            Sign In
                                        </Link>
                                        <Link 
                                            href={route('register')} 
                                            className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md text-center"
                                        >
                                            Get Started
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </nav>
            
            {/* Spacer for fixed header */}
            <div className="h-14 sm:h-16" />

            {/* Main Content */}
            <main>
                {children}
            </main>

            {/* Footer */}
            <footer className="py-12 border-t border-theme-primary">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                        <div className="col-span-2 md:col-span-1">
                            <Link href="/" className="flex items-center gap-2 mb-4">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                                    <div className="h-4 w-4 text-white">{Icons.phone}</div>
                                </div>
                                <span className="text-base font-semibold text-theme-primary">BrandCall</span>
                            </Link>
                            <p className="text-sm text-theme-muted leading-relaxed">
                                Enterprise branded caller ID platform for businesses that need compliance and scale.
                            </p>
                        </div>
                        <div>
                            <h4 className="text-sm font-semibold text-theme-primary mb-4">Product</h4>
                            <ul className="space-y-3">
                                {footerLinks.product.map((link) => (
                                    <li key={link.href}>
                                        <Link href={link.href} className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-sm font-semibold text-theme-primary mb-4">Resources</h4>
                            <ul className="space-y-3">
                                {footerLinks.resources.map((link) => (
                                    <li key={link.href}>
                                        {link.external ? (
                                            <a href={link.href} className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">
                                                {link.label}
                                            </a>
                                        ) : (
                                            <Link href={link.href} className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">
                                                {link.label}
                                            </Link>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-sm font-semibold text-theme-primary mb-4">Legal</h4>
                            <ul className="space-y-3">
                                {footerLinks.legal.map((link) => (
                                    <li key={link.label}>
                                        <Link href={link.href} className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                    <div className="pt-8 border-t border-theme-primary flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p className="text-sm text-theme-muted">
                            © {new Date().getFullYear()} BrandCall. All rights reserved.
                        </p>
                        <div className="flex items-center gap-6">
                            <a href="#" className="text-theme-muted hover:text-theme-primary transition-colors">
                                <span className="sr-only">LinkedIn</span>
                                <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="#" className="text-theme-muted hover:text-theme-primary transition-colors">
                                <span className="sr-only">Twitter</span>
                                <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
