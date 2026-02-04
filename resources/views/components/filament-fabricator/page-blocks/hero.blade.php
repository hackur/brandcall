@props(['eyebrow', 'headlines', 'description', 'primary_cta_text', 'primary_cta_url', 'secondary_cta_text', 'secondary_cta_url', 'background_style', 'show_indicators'])

<section class="py-24 lg:py-32">
    <div class="max-w-7xl mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
            @if($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-400 mb-6">
                    {{ $eyebrow }}
                </p>
            @endif

            @if(!empty($headlines))
                <div 
                    x-data="{ 
                        current: 0, 
                        visible: true,
                        headlines: {{ json_encode($headlines) }},
                        rotate() {
                            this.visible = false;
                            setTimeout(() => {
                                this.current = (this.current + 1) % this.headlines.length;
                                this.visible = true;
                            }, 300);
                        }
                    }"
                    x-init="setInterval(() => rotate(), 5000)"
                    class="h-32 sm:h-36 lg:h-40 flex items-center justify-center mb-8"
                >
                    <h1 
                        class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight transition-opacity duration-300"
                        :class="visible ? 'opacity-100' : 'opacity-0'"
                    >
                        <span 
                            class="bg-gradient-to-r from-brand-400 via-purple-400 to-brand-400 bg-clip-text text-transparent"
                            x-text="headlines[current]?.title"
                        ></span>
                        <br>
                        <span class="text-white" x-text="headlines[current]?.subtitle"></span>
                    </h1>
                </div>

                @if($show_indicators ?? true)
                    <div 
                        x-data="{ current: 0 }"
                        class="flex justify-center gap-2 mt-12"
                    >
                        @foreach($headlines as $index => $headline)
                            <button
                                @click="current = {{ $index }}"
                                class="h-1.5 rounded-full transition-all duration-200"
                                :class="{{ $index }} === current ? 'w-8 bg-brand-500' : 'w-1.5 bg-slate-700 hover:bg-slate-600'"
                                aria-label="Show headline {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($description)
                <p class="text-lg text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                    {{ $description }}
                </p>
            @endif

            @if($primary_cta_text || $secondary_cta_text)
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @if($primary_cta_text)
                        <a 
                            href="{{ $primary_cta_url ?? '/register' }}" 
                            class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-brand-500 to-brand-600 rounded-lg shadow-lg shadow-brand-600/25 hover:shadow-xl hover:shadow-brand-600/30 hover:-translate-y-0.5 transition-all duration-200"
                        >
                            {{ $primary_cta_text }}
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endif
                    
                    @if($secondary_cta_text)
                        <a 
                            href="{{ $secondary_cta_url ?? '#' }}"
                            class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-800 hover:border-slate-600 hover:text-white transition-colors"
                        >
                            {{ $secondary_cta_text }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
