@props(['heading', 'description', 'primary_cta_text', 'primary_cta_url', 'secondary_cta_text', 'secondary_cta_url', 'style'])

@php
    $isCard = ($style ?? 'card') === 'card';
@endphp

<section class="py-24 border-t border-slate-800/50">
    <div class="max-w-4xl mx-auto px-6">
        <div class="{{ $isCard ? 'text-center p-12 lg:p-16 rounded-2xl bg-gradient-to-b from-slate-800/50 to-slate-800/30 border border-slate-700/50' : 'text-center' }}">
            @if($heading)
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">{{ $heading }}</h2>
            @endif
            
            @if($description)
                <p class="text-lg text-slate-400 max-w-xl mx-auto mb-8">{{ $description }}</p>
            @endif
            
            @if($primary_cta_text || $secondary_cta_text)
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @if($primary_cta_text)
                        <a 
                            href="{{ $primary_cta_url ?? '/register' }}" 
                            class="inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-brand-500 to-brand-600 rounded-lg shadow-lg shadow-brand-600/25 hover:shadow-xl hover:shadow-brand-600/30 hover:-translate-y-0.5 transition-all duration-200"
                        >
                            {{ $primary_cta_text }}
                        </a>
                    @endif
                    
                    @if($secondary_cta_text)
                        <a 
                            href="{{ $secondary_cta_url ?? '#' }}" 
                            class="inline-flex items-center px-8 py-4 text-base font-semibold text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-800 hover:border-slate-600 hover:text-white transition-colors"
                        >
                            {{ $secondary_cta_text }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
