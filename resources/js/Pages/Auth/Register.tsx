import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect, useCallback, useRef, KeyboardEvent } from 'react';
import axios from 'axios';

// Simple debounce utility
function debounce<T extends (...args: any[]) => any>(fn: T, delay: number): T & { flush: () => void; cancel: () => void } {
    let timeoutId: ReturnType<typeof setTimeout> | null = null;
    let lastArgs: Parameters<T> | null = null;
    
    const debouncedFn = ((...args: Parameters<T>) => {
        lastArgs = args;
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            fn(...args);
            timeoutId = null;
        }, delay);
    }) as T & { flush: () => void; cancel: () => void };
    
    debouncedFn.flush = () => {
        if (timeoutId && lastArgs) {
            clearTimeout(timeoutId);
            fn(...lastArgs);
            timeoutId = null;
        }
    };
    
    debouncedFn.cancel = () => {
        if (timeoutId) {
            clearTimeout(timeoutId);
            timeoutId = null;
        }
    };
    
    return debouncedFn;
}

interface FormData {
    // Step 1: Account
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    // Step 2: Business
    company_name: string;
    company_website: string;
    company_phone: string;
    company_address: string;
    company_city: string;
    company_state: string;
    company_zip: string;
    company_country: string;
    // Step 3: KYC
    industry: string;
    monthly_call_volume: string;
    use_case: string;
    current_provider: string;
    has_stir_shaken: string;
    // Step 4: Phone Numbers
    primary_phone: string;
    phone_ownership: string;
}

interface DraftStatus {
    saving: boolean;
    lastSaved: Date | null;
    error: string | null;
}

const steps = [
    { id: 1, name: 'Account', description: 'Your login credentials' },
    { id: 2, name: 'Business', description: 'Company information' },
    { id: 3, name: 'Qualification', description: 'Calling needs & KYC' },
    { id: 4, name: 'Phone Numbers', description: 'Numbers to brand' },
];

const industries = [
    'Healthcare / Medical',
    'Financial Services / Banking',
    'Insurance',
    'Real Estate',
    'Retail / E-commerce',
    'Telecommunications',
    'Technology / SaaS',
    'Education',
    'Government',
    'Non-profit',
    'Legal Services',
    'Automotive',
    'Travel / Hospitality',
    'Collections',
    'Other',
];

const volumeOptions = [
    { value: 'under_1k', label: 'Under 1,000 calls/month' },
    { value: '1k_10k', label: '1,000 - 10,000 calls/month' },
    { value: '10k_100k', label: '10,000 - 100,000 calls/month' },
    { value: '100k_1m', label: '100,000 - 1M calls/month' },
    { value: 'over_1m', label: 'Over 1M calls/month' },
];

const useCases = [
    'Customer service callbacks',
    'Appointment reminders',
    'Delivery notifications',
    'Sales outreach',
    'Account alerts / Fraud prevention',
    'Collections',
    'Political campaigns',
    'Surveys / Research',
    'Other',
];

export default function Register() {
    const [currentStep, setCurrentStep] = useState(1);
    const [draftStatus, setDraftStatus] = useState<DraftStatus>({
        saving: false,
        lastSaved: null,
        error: null,
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const draftLoadedRef = useRef(false);
    const saveDraftRef = useRef<ReturnType<typeof debounce> | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm<FormData>({
        // Step 1
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        // Step 2
        company_name: '',
        company_website: '',
        company_phone: '',
        company_address: '',
        company_city: '',
        company_state: '',
        company_zip: '',
        company_country: 'US',
        // Step 3
        industry: '',
        monthly_call_volume: '',
        use_case: '',
        current_provider: '',
        has_stir_shaken: '',
        // Step 4
        primary_phone: '',
        phone_ownership: '',
    });

    // Create debounced save function
    useEffect(() => {
        saveDraftRef.current = debounce(async (formData: FormData, step: number) => {
            // Don't save if submitting or no meaningful data
            if (isSubmitting || (!formData.name && !formData.email && !formData.company_name)) {
                return;
            }

            setDraftStatus(prev => ({ ...prev, saving: true, error: null }));

            try {
                const response = await axios.post('/register/draft', {
                    ...formData,
                    current_step: step,
                });

                if (response.data.success) {
                    setDraftStatus({
                        saving: false,
                        lastSaved: new Date(),
                        error: null,
                    });
                }
            } catch (error: any) {
                // Ignore errors during/after submission
                if (!isSubmitting) {
                    setDraftStatus(prev => ({
                        ...prev,
                        saving: false,
                        error: 'Failed to save draft',
                    }));
                }
                console.error('Failed to save draft:', error);
            }
        }, 1500);

        return () => {
            saveDraftRef.current?.cancel();
        };
    }, [isSubmitting]);

    // Load draft on mount
    useEffect(() => {
        loadDraft();
    }, []);

    const loadDraft = async () => {
        try {
            const response = await axios.get('/register/draft');
            if (response.data.draft) {
                const draft = response.data.draft;
                // Update form with draft data (excluding password fields)
                const fieldsToRestore: (keyof FormData)[] = [
                    'name', 'email', 'company_name', 'company_website', 'company_phone',
                    'company_address', 'company_city', 'company_state', 'company_zip',
                    'company_country', 'industry', 'monthly_call_volume', 'use_case',
                    'current_provider', 'has_stir_shaken', 'primary_phone', 'phone_ownership'
                ];
                
                fieldsToRestore.forEach((key) => {
                    if (draft[key]) {
                        setData(key, draft[key]);
                    }
                });
                
                if (draft.current_step && draft.current_step > 1) {
                    setCurrentStep(draft.current_step);
                }
                if (draft.last_saved_at) {
                    setDraftStatus(prev => ({
                        ...prev,
                        lastSaved: new Date(draft.last_saved_at),
                    }));
                }
                draftLoadedRef.current = true;
            }
        } catch (error) {
            console.error('Failed to load draft:', error);
        }
    };

    // Save draft function (called manually)
    const saveDraft = useCallback((formData: FormData, step: number) => {
        if (!isSubmitting && saveDraftRef.current) {
            saveDraftRef.current(formData, step);
        }
    }, [isSubmitting]);

    // Field change handler
    const handleFieldChange = (field: keyof FormData, value: string) => {
        setData(field, value);
    };

    // Field blur handler - save draft
    const handleFieldBlur = () => {
        if (!isSubmitting) {
            saveDraft(data, currentStep);
        }
    };

    // Prevent Enter key from submitting form on non-final steps
    const handleKeyDown = (e: KeyboardEvent<HTMLFormElement>) => {
        if (e.key === 'Enter' && currentStep < 4) {
            e.preventDefault();
            if (validateStep(currentStep)) {
                nextStep();
            }
        }
    };

    const validateStep = (step: number): boolean => {
        switch (step) {
            case 1:
                return !!(data.name && data.email && data.password && data.password === data.password_confirmation && data.password.length >= 8);
            case 2:
                return !!(data.company_name);
            case 3:
                return true;
            case 4:
                return true;
            default:
                return true;
        }
    };

    const nextStep = () => {
        if (validateStep(currentStep) && currentStep < 4) {
            const newStep = currentStep + 1;
            setCurrentStep(newStep);
            saveDraft(data, newStep);
        }
    };

    const prevStep = () => {
        if (currentStep > 1) {
            setCurrentStep(currentStep - 1);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        
        // Only allow submission from step 4
        if (currentStep !== 4) {
            console.warn('Form submitted from wrong step:', currentStep);
            return;
        }
        
        // Prevent double submission
        if (isSubmitting || processing) {
            return;
        }

        // Cancel any pending draft saves
        saveDraftRef.current?.cancel();
        
        // Mark as submitting to prevent draft saves
        setIsSubmitting(true);
        
        // Submit the form
        post(route('register'), {
            onFinish: () => {
                // Only reset submitting state if we stay on the page (error case)
                // On success, we'll redirect so this won't matter
                setIsSubmitting(false);
            },
        });
    };

    // Format time ago for draft status
    const formatTimeAgo = (date: Date): string => {
        const seconds = Math.floor((new Date().getTime() - date.getTime()) / 1000);
        if (seconds < 60) return 'just now';
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
        return date.toLocaleDateString();
    };

    return (
        <>
            <Head title="Get Started - BrandCall" />
            
            <div className="relative min-h-screen overflow-hidden">
                {/* Animated Background */}
                <div className="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950" />
                <div className="absolute inset-0 overflow-hidden">
                    <div className="animate-blob absolute -left-40 -top-40 h-96 w-96 rounded-full bg-purple-600/10 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-2000 absolute -right-40 top-20 h-96 w-96 rounded-full bg-indigo-600/10 mix-blend-screen blur-3xl filter" />
                </div>

                <div className="relative z-10 flex min-h-screen flex-col">
                    {/* Header */}
                    <nav className="px-4 py-3 sm:px-6 sm:py-4">
                        <div className="mx-auto flex max-w-7xl items-center justify-between">
                            <Link href="/" className="flex items-center gap-2">
                                <div className="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600">
                                    <svg className="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span className="text-lg sm:text-xl font-bold text-white">BrandCall</span>
                            </Link>
                            <div className="flex items-center gap-2 sm:gap-4">
                                {/* Draft Status Indicator */}
                                {!isSubmitting && (draftStatus.saving || draftStatus.lastSaved) && (
                                    <div className="flex items-center gap-2 text-sm">
                                        {draftStatus.saving ? (
                                            <>
                                                <svg className="h-4 w-4 animate-spin text-purple-400" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                </svg>
                                                <span className="text-gray-400">Saving...</span>
                                            </>
                                        ) : draftStatus.lastSaved ? (
                                            <>
                                                <svg className="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span className="text-gray-400">
                                                    Draft saved {formatTimeAgo(draftStatus.lastSaved)}
                                                </span>
                                            </>
                                        ) : null}
                                    </div>
                                )}
                                <Link href={route('login')} className="text-xs sm:text-sm text-gray-400 hover:text-white">
                                    <span className="hidden sm:inline">Already have an account? </span><span className="text-purple-400">Sign in</span>
                                </Link>
                            </div>
                        </div>
                    </nav>

                    {/* Main Content */}
                    <div className="flex flex-1 items-center justify-center px-4 py-4 sm:px-6 sm:py-8">
                        <div className="w-full max-w-2xl">
                            {/* Progress Steps */}
                            <div className="mb-4 sm:mb-6">
                                <div className="flex items-center justify-between">
                                    {steps.map((step, index) => (
                                        <div key={step.id} className="flex flex-1 items-center">
                                            <div className="flex flex-col items-center">
                                                <div
                                                    className={`flex h-7 w-7 sm:h-9 sm:w-9 items-center justify-center rounded-full border-2 text-xs sm:text-sm font-semibold transition-all ${
                                                        currentStep > step.id
                                                            ? 'border-purple-500 bg-purple-500 text-white'
                                                            : currentStep === step.id
                                                            ? 'border-purple-500 bg-purple-500/20 text-purple-400'
                                                            : 'border-gray-700 text-gray-500'
                                                    }`}
                                                >
                                                    {currentStep > step.id ? (
                                                        <svg className="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                        </svg>
                                                    ) : (
                                                        step.id
                                                    )}
                                                </div>
                                                <div className="mt-1 sm:mt-2 text-center">
                                                    <div className={`text-[10px] sm:text-xs font-medium ${currentStep >= step.id ? 'text-white' : 'text-gray-500'}`}>
                                                        {step.name}
                                                    </div>
                                                </div>
                                            </div>
                                            {index < steps.length - 1 && (
                                                <div className={`mx-1 sm:mx-2 h-0.5 flex-1 ${currentStep > step.id ? 'bg-purple-500' : 'bg-gray-800'}`} />
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Form Card */}
                            <div className="rounded-xl sm:rounded-2xl border border-gray-800 bg-gray-900/80 p-4 sm:p-6 backdrop-blur">
                                <form onSubmit={submit} onKeyDown={handleKeyDown}>
                                    {/* Step 1: Account */}
                                    {currentStep === 1 && (
                                        <div className="space-y-3 sm:space-y-4">
                                            <div>
                                                <h2 className="text-lg sm:text-xl font-bold text-white">Create your account</h2>
                                                <p className="text-sm text-gray-400">Start with your login credentials</p>
                                            </div>

                                            <div className="grid gap-3 sm:gap-4 sm:grid-cols-2">
                                                <div className="sm:col-span-2">
                                                    <InputLabel htmlFor="name" value="Full Name" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="name"
                                                        value={data.name}
                                                        onChange={(e) => handleFieldChange('name', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        required
                                                    />
                                                    <InputError message={errors.name} className="mt-1" />
                                                </div>

                                                <div className="sm:col-span-2">
                                                    <InputLabel htmlFor="email" value="Work Email" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="email"
                                                        type="email"
                                                        value={data.email}
                                                        onChange={(e) => handleFieldChange('email', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        required
                                                    />
                                                    <InputError message={errors.email} className="mt-1" />
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="password" value="Password" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="password"
                                                        type="password"
                                                        value={data.password}
                                                        onChange={(e) => handleFieldChange('password', e.target.value)}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        required
                                                    />
                                                    <InputError message={errors.password} className="mt-1" />
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="password_confirmation"
                                                        type="password"
                                                        value={data.password_confirmation}
                                                        onChange={(e) => handleFieldChange('password_confirmation', e.target.value)}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Step 2: Business Info */}
                                    {currentStep === 2 && (
                                        <div className="space-y-3 sm:space-y-4">
                                            <div>
                                                <h2 className="text-lg sm:text-xl font-bold text-white">Business Information</h2>
                                                <p className="text-sm text-gray-400">Tell us about your company</p>
                                            </div>

                                            <div className="grid gap-3 sm:gap-4 sm:grid-cols-2">
                                                <div className="sm:col-span-2">
                                                    <InputLabel htmlFor="company_name" value="Company Name *" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="company_name"
                                                        value={data.company_name}
                                                        onChange={(e) => handleFieldChange('company_name', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="Acme Corporation"
                                                        required
                                                    />
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="company_website" value="Website" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="company_website"
                                                        value={data.company_website}
                                                        onChange={(e) => handleFieldChange('company_website', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="https://example.com"
                                                    />
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="company_phone" value="Business Phone" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="company_phone"
                                                        value={data.company_phone}
                                                        onChange={(e) => handleFieldChange('company_phone', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="+1 (555) 000-0000"
                                                    />
                                                </div>

                                                <div className="sm:col-span-2">
                                                    <InputLabel htmlFor="company_address" value="Street Address" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="company_address"
                                                        value={data.company_address}
                                                        onChange={(e) => handleFieldChange('company_address', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="123 Business Ave"
                                                    />
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="company_city" value="City" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="company_city"
                                                        value={data.company_city}
                                                        onChange={(e) => handleFieldChange('company_city', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                    />
                                                </div>

                                                <div className="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <InputLabel htmlFor="company_state" value="State" className="text-gray-300 text-sm" />
                                                        <TextInput
                                                            id="company_state"
                                                            value={data.company_state}
                                                            onChange={(e) => handleFieldChange('company_state', e.target.value)}
                                                            onBlur={handleFieldBlur}
                                                            className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel htmlFor="company_zip" value="ZIP" className="text-gray-300 text-sm" />
                                                        <TextInput
                                                            id="company_zip"
                                                            value={data.company_zip}
                                                            onChange={(e) => handleFieldChange('company_zip', e.target.value)}
                                                            onBlur={handleFieldBlur}
                                                            className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Step 3: KYC / Qualification */}
                                    {currentStep === 3 && (
                                        <div className="space-y-3 sm:space-y-4">
                                            <div>
                                                <h2 className="text-lg sm:text-xl font-bold text-white">Qualification</h2>
                                                <p className="text-sm text-gray-400">Help us understand your calling needs (optional)</p>
                                            </div>

                                            <div className="grid gap-3 sm:gap-4">
                                                <div>
                                                    <InputLabel htmlFor="industry" value="Industry" className="text-gray-300 text-sm" />
                                                    <select
                                                        id="industry"
                                                        value={data.industry}
                                                        onChange={(e) => handleFieldChange('industry', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                    >
                                                        <option value="">Select your industry</option>
                                                        {industries.map((industry) => (
                                                            <option key={industry} value={industry}>{industry}</option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="monthly_call_volume" value="Estimated Monthly Call Volume" className="text-gray-300 text-sm" />
                                                    <select
                                                        id="monthly_call_volume"
                                                        value={data.monthly_call_volume}
                                                        onChange={(e) => handleFieldChange('monthly_call_volume', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                    >
                                                        <option value="">Select volume</option>
                                                        {volumeOptions.map((option) => (
                                                            <option key={option.value} value={option.value}>{option.label}</option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="use_case" value="Primary Use Case" className="text-gray-300 text-sm" />
                                                    <select
                                                        id="use_case"
                                                        value={data.use_case}
                                                        onChange={(e) => handleFieldChange('use_case', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                    >
                                                        <option value="">Select use case</option>
                                                        {useCases.map((useCase) => (
                                                            <option key={useCase} value={useCase}>{useCase}</option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div>
                                                    <InputLabel htmlFor="current_provider" value="Current Voice/Calling Provider" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="current_provider"
                                                        value={data.current_provider}
                                                        onChange={(e) => handleFieldChange('current_provider', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="e.g., Twilio, RingCentral, Vonage"
                                                    />
                                                </div>

                                                <div>
                                                    <InputLabel value="Are you currently using STIR/SHAKEN?" className="text-gray-300 text-sm" />
                                                    <div className="mt-1.5 flex flex-wrap gap-3 sm:gap-4">
                                                        {['Yes', 'No', 'Not sure'].map((option) => (
                                                            <label key={option} className="flex items-center">
                                                                <input
                                                                    type="radio"
                                                                    name="has_stir_shaken"
                                                                    value={option.toLowerCase().replace(' ', '_')}
                                                                    checked={data.has_stir_shaken === option.toLowerCase().replace(' ', '_')}
                                                                    onChange={(e) => handleFieldChange('has_stir_shaken', e.target.value)}
                                                                    className="h-3.5 w-3.5 border-gray-700 bg-gray-800 text-purple-500 focus:ring-purple-500"
                                                                />
                                                                <span className="ml-1.5 text-sm text-gray-300">{option}</span>
                                                            </label>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Step 4: Phone Numbers */}
                                    {currentStep === 4 && (
                                        <div className="space-y-3 sm:space-y-4">
                                            <div>
                                                <h2 className="text-lg sm:text-xl font-bold text-white">Phone Numbers</h2>
                                                <p className="text-sm text-gray-400">Phone numbers you want to brand (optional)</p>
                                            </div>

                                            <div className="grid gap-3 sm:gap-4">
                                                <div>
                                                    <InputLabel htmlFor="primary_phone" value="Primary Outbound Number" className="text-gray-300 text-sm" />
                                                    <TextInput
                                                        id="primary_phone"
                                                        value={data.primary_phone}
                                                        onChange={(e) => handleFieldChange('primary_phone', e.target.value)}
                                                        onBlur={handleFieldBlur}
                                                        className="mt-1 block w-full border-gray-700 bg-gray-800 text-white text-sm py-2 focus:border-purple-500 focus:ring-purple-500"
                                                        placeholder="+1 (555) 000-0000"
                                                    />
                                                    <p className="mt-0.5 text-xs text-gray-500">
                                                        You can add more numbers after registration
                                                    </p>
                                                </div>

                                                <div>
                                                    <InputLabel value="Phone Number Ownership" className="text-gray-300 text-sm" />
                                                    <div className="mt-1.5 space-y-1.5">
                                                        {[
                                                            { value: 'own', label: 'I own this number (have LOA/documentation)' },
                                                            { value: 'provider', label: 'Number is from my voice provider' },
                                                            { value: 'need', label: 'I need to acquire new numbers' },
                                                        ].map((option) => (
                                                            <label key={option.value} className="flex items-center">
                                                                <input
                                                                    type="radio"
                                                                    name="phone_ownership"
                                                                    value={option.value}
                                                                    checked={data.phone_ownership === option.value}
                                                                    onChange={(e) => handleFieldChange('phone_ownership', e.target.value)}
                                                                    className="h-3.5 w-3.5 border-gray-700 bg-gray-800 text-purple-500 focus:ring-purple-500"
                                                                />
                                                                <span className="ml-1.5 text-sm text-gray-300">{option.label}</span>
                                                            </label>
                                                        ))}
                                                    </div>
                                                </div>

                                                {/* Summary */}
                                                <div className="mt-2 rounded-lg border border-gray-700 bg-gray-800/50 p-3">
                                                    <h3 className="text-sm font-medium text-white">Registration Summary</h3>
                                                    <dl className="mt-2 space-y-1 text-xs sm:text-sm">
                                                        <div className="flex justify-between">
                                                            <dt className="text-gray-400">Account</dt>
                                                            <dd className="text-white truncate ml-2">{data.email || 'Not set'}</dd>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <dt className="text-gray-400">Company</dt>
                                                            <dd className="text-white truncate ml-2">{data.company_name || 'Not set'}</dd>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <dt className="text-gray-400">Industry</dt>
                                                            <dd className="text-white truncate ml-2">{data.industry || 'Not specified'}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Navigation */}
                                    <div className="mt-4 sm:mt-6 flex items-center justify-between border-t border-gray-800 pt-4">
                                        <div>
                                            {currentStep > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={prevStep}
                                                    disabled={isSubmitting}
                                                    className="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white disabled:opacity-50"
                                                >
                                                    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                    Back
                                                </button>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-3">
                                            {currentStep < 4 && (
                                                <button
                                                    key={`continue-${currentStep}`}
                                                    type="button"
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        nextStep();
                                                    }}
                                                    disabled={!validateStep(currentStep)}
                                                    className="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition-all hover:shadow-purple-500/40 disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                    Continue
                                                    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            )}
                                            {currentStep === 4 && (
                                                <button
                                                    key="submit-account"
                                                    type="submit"
                                                    disabled={isSubmitting || processing || !validateStep(1)}
                                                    className="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition-all hover:shadow-purple-500/40 disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                    {(isSubmitting || processing) ? (
                                                        <>
                                                            <svg className="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                            </svg>
                                                            Creating...
                                                        </>
                                                    ) : (
                                                        <>
                                                            Create Account
                                                            <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </>
                                                    )}
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {/* Skip link */}
                            {currentStep > 1 && currentStep < 4 && !isSubmitting && (
                                <div className="mt-4 text-center">
                                    <button
                                        type="button"
                                        onClick={() => setCurrentStep(4)}
                                        className="text-sm text-gray-500 hover:text-gray-400"
                                    >
                                        Skip to finish →
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* CSS for animations */}
                <style>{`
                    @keyframes blob {
                        0%, 100% { transform: translate(0, 0) scale(1); }
                        25% { transform: translate(20px, -30px) scale(1.1); }
                        50% { transform: translate(-20px, 20px) scale(0.9); }
                        75% { transform: translate(30px, 10px) scale(1.05); }
                    }
                    .animate-blob {
                        animation: blob 15s infinite ease-in-out;
                    }
                    .animation-delay-2000 {
                        animation-delay: 2s;
                    }
                `}</style>
            </div>
        </>
    );
}
