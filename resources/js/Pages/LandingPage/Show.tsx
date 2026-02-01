import { Head, useForm } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

interface FormField {
    name: string;
    label: string;
    type: string;
    required: boolean;
}

interface Feature {
    title: string;
    description: string;
    icon?: string;
}

interface Testimonial {
    name: string;
    title: string;
    quote: string;
    avatar?: string;
}

interface Colors {
    primary: string;
    secondary: string;
    accent: string;
    background: string;
    text: string;
}

interface LandingPageData {
    id: number;
    slug: string;
    name: string;
    headline: string;
    subheadline: string | null;
    description: string | null;
    cta_text: string;
    cta_url: string | null;
    hero_image: string | null;
    hero_video_url: string | null;
    features: Feature[];
    testimonials: Testimonial[];
    show_pricing: boolean;
    pricing_headline: string | null;
    show_contact_form: boolean;
    form_headline: string;
    form_fields: FormField[];
    layout_preset: string;
    color_scheme: string;
    colors: Colors;
    logo_url: string | null;
    meta_title: string;
    meta_description: string | null;
    og_image: string | null;
}

interface Props {
    page: LandingPageData;
}

export default function Show({ page }: Props) {
    const [submitted, setSubmitted] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm<Record<string, string>>({});

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/lp/${page.slug}/lead`, {
            preserveScroll: true,
            onSuccess: () => {
                setSubmitted(true);
                reset();
            },
        });
    };

    const colors = page.colors;

    // Generate CSS variables
    const cssVars = {
        '--color-primary': colors.primary,
        '--color-secondary': colors.secondary,
        '--color-accent': colors.accent,
        '--color-background': colors.background,
        '--color-text': colors.text,
    } as React.CSSProperties;

    const layoutClasses = {
        'hero-left': 'flex-row',
        'hero-right': 'flex-row-reverse',
        'hero-center': 'flex-col items-center text-center',
        'hero-fullwidth': 'flex-col',
        'minimal': 'flex-col items-center text-center',
        'corporate': 'flex-row',
        'startup': 'flex-col items-center text-center',
        'saas': 'flex-row',
    };

    return (
        <>
            <Head>
                <title>{page.meta_title}</title>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
                {page.og_image && <meta property="og:image" content={page.og_image} />}
            </Head>

            <div className="min-h-screen" style={{ ...cssVars, backgroundColor: colors.background }}>
                {/* Header */}
                {page.logo_url && (
                    <header className="py-4 px-6">
                        <img src={page.logo_url} alt="Logo" className="h-10" />
                    </header>
                )}

                {/* Hero Section */}
                <section className="py-20 px-6">
                    <div className={`max-w-6xl mx-auto flex gap-12 ${layoutClasses[page.layout_preset as keyof typeof layoutClasses] || 'flex-row'}`}>
                        <div className="flex-1">
                            <h1 
                                className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6"
                                style={{ color: colors.text }}
                            >
                                {page.headline}
                            </h1>
                            {page.subheadline && (
                                <p 
                                    className="text-xl md:text-2xl mb-6 opacity-80"
                                    style={{ color: colors.text }}
                                >
                                    {page.subheadline}
                                </p>
                            )}
                            {page.description && (
                                <p 
                                    className="text-lg mb-8 opacity-70"
                                    style={{ color: colors.text }}
                                >
                                    {page.description}
                                </p>
                            )}
                            {page.cta_text && (
                                <a
                                    href={page.cta_url || '#contact'}
                                    className="inline-block px-8 py-4 text-lg font-semibold rounded-lg transition-transform hover:scale-105"
                                    style={{ 
                                        backgroundColor: colors.primary, 
                                        color: '#ffffff' 
                                    }}
                                >
                                    {page.cta_text}
                                </a>
                            )}
                        </div>
                        {page.hero_image && !['hero-center', 'minimal', 'startup'].includes(page.layout_preset) && (
                            <div className="flex-1">
                                <img 
                                    src={page.hero_image} 
                                    alt="Hero" 
                                    className="rounded-lg shadow-2xl w-full"
                                />
                            </div>
                        )}
                    </div>
                </section>

                {/* Features Section */}
                {page.features && page.features.length > 0 && (
                    <section className="py-20 px-6" style={{ backgroundColor: colors.secondary + '10' }}>
                        <div className="max-w-6xl mx-auto">
                            <h2 
                                className="text-3xl md:text-4xl font-bold text-center mb-12"
                                style={{ color: colors.text }}
                            >
                                Why Choose Us
                            </h2>
                            <div className="grid md:grid-cols-3 gap-8">
                                {page.features.map((feature, index) => (
                                    <div 
                                        key={index}
                                        className="p-6 rounded-lg bg-white shadow-lg"
                                    >
                                        {feature.icon && (
                                            <div 
                                                className="w-12 h-12 rounded-full flex items-center justify-center mb-4"
                                                style={{ backgroundColor: colors.primary + '20' }}
                                            >
                                                <span style={{ color: colors.primary }}>✓</span>
                                            </div>
                                        )}
                                        <h3 
                                            className="text-xl font-semibold mb-2"
                                            style={{ color: colors.text }}
                                        >
                                            {feature.title}
                                        </h3>
                                        <p className="opacity-70" style={{ color: colors.text }}>
                                            {feature.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* Testimonials Section */}
                {page.testimonials && page.testimonials.length > 0 && (
                    <section className="py-20 px-6">
                        <div className="max-w-6xl mx-auto">
                            <h2 
                                className="text-3xl md:text-4xl font-bold text-center mb-12"
                                style={{ color: colors.text }}
                            >
                                What Our Customers Say
                            </h2>
                            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                                {page.testimonials.map((testimonial, index) => (
                                    <div 
                                        key={index}
                                        className="p-6 rounded-lg bg-white shadow-lg"
                                    >
                                        <p 
                                            className="text-lg mb-4 italic"
                                            style={{ color: colors.text }}
                                        >
                                            "{testimonial.quote}"
                                        </p>
                                        <div className="flex items-center gap-3">
                                            {testimonial.avatar ? (
                                                <img 
                                                    src={testimonial.avatar} 
                                                    alt={testimonial.name}
                                                    className="w-12 h-12 rounded-full object-cover"
                                                />
                                            ) : (
                                                <div 
                                                    className="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
                                                    style={{ backgroundColor: colors.primary }}
                                                >
                                                    {testimonial.name.charAt(0)}
                                                </div>
                                            )}
                                            <div>
                                                <p className="font-semibold" style={{ color: colors.text }}>
                                                    {testimonial.name}
                                                </p>
                                                {testimonial.title && (
                                                    <p className="text-sm opacity-70" style={{ color: colors.text }}>
                                                        {testimonial.title}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* Contact Form Section */}
                {page.show_contact_form && (
                    <section id="contact" className="py-20 px-6" style={{ backgroundColor: colors.secondary + '10' }}>
                        <div className="max-w-xl mx-auto">
                            <h2 
                                className="text-3xl md:text-4xl font-bold text-center mb-8"
                                style={{ color: colors.text }}
                            >
                                {page.form_headline}
                            </h2>

                            {submitted ? (
                                <div 
                                    className="p-8 rounded-lg text-center"
                                    style={{ backgroundColor: colors.primary + '20' }}
                                >
                                    <div 
                                        className="text-4xl mb-4"
                                        style={{ color: colors.primary }}
                                    >
                                        ✓
                                    </div>
                                    <h3 
                                        className="text-2xl font-semibold mb-2"
                                        style={{ color: colors.text }}
                                    >
                                        Thank You!
                                    </h3>
                                    <p style={{ color: colors.text }} className="opacity-70">
                                        We'll be in touch soon.
                                    </p>
                                </div>
                            ) : (
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    {page.form_fields.map((field) => (
                                        <div key={field.name}>
                                            <label 
                                                className="block text-sm font-medium mb-1"
                                                style={{ color: colors.text }}
                                            >
                                                {field.label}
                                                {field.required && <span className="text-red-500">*</span>}
                                            </label>
                                            {field.type === 'textarea' ? (
                                                <textarea
                                                    name={field.name}
                                                    value={data[field.name] || ''}
                                                    onChange={(e) => setData(field.name, e.target.value)}
                                                    required={field.required}
                                                    rows={4}
                                                    className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent"
                                                    style={{ 
                                                        outlineColor: colors.primary,
                                                    }}
                                                />
                                            ) : (
                                                <input
                                                    type={field.type}
                                                    name={field.name}
                                                    value={data[field.name] || ''}
                                                    onChange={(e) => setData(field.name, e.target.value)}
                                                    required={field.required}
                                                    className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent"
                                                    style={{ 
                                                        outlineColor: colors.primary,
                                                    }}
                                                />
                                            )}
                                            {errors[field.name] && (
                                                <p className="text-red-500 text-sm mt-1">{errors[field.name]}</p>
                                            )}
                                        </div>
                                    ))}
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full py-4 text-lg font-semibold rounded-lg transition-opacity disabled:opacity-50"
                                        style={{ 
                                            backgroundColor: colors.primary, 
                                            color: '#ffffff' 
                                        }}
                                    >
                                        {processing ? 'Submitting...' : 'Submit'}
                                    </button>
                                </form>
                            )}
                        </div>
                    </section>
                )}

                {/* Footer */}
                <footer className="py-8 px-6 text-center" style={{ backgroundColor: colors.secondary }}>
                    <p style={{ color: '#ffffff' }} className="opacity-70">
                        © {new Date().getFullYear()} BrandCall. All rights reserved.
                    </p>
                </footer>
            </div>
        </>
    );
}
