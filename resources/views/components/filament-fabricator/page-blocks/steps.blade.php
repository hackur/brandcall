@props(['eyebrow', 'heading', 'description', 'steps', 'show_connectors', 'auto_number'])

<section class="py-24 border-t border-slate-800/50">
    <div class="max-w-7xl mx-auto px-6">
        @if($eyebrow || $heading || $description)
            <div class="text-center mb-16">
                @if($eyebrow)
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-400 mb-4">{{ $eyebrow }}</p>
                @endif
                @if($heading)
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">{{ $heading }}</h2>
                @endif
                @if($description)
                    <p class="text-lg text-slate-400 max-w-2xl mx-auto">{{ $description }}</p>
                @endif
            </div>
        @endif

        @if(!empty($steps))
            <div class="grid md:grid-cols-2 lg:grid-cols-{{ min(count($steps), 4) }} gap-8 lg:gap-12">
                @foreach($steps as $index => $step)
                    <div class="relative">
                        @if(($show_connectors ?? true) && $index < count($steps) - 1)
                            <div class="hidden lg:block absolute top-6 left-full w-full h-px bg-gradient-to-r from-slate-700 to-transparent"></div>
                        @endif
                        
                        <div class="text-center lg:text-left">
                            @if($auto_number ?? true)
                                <span class="text-5xl font-extrabold text-brand-600/20">
                                    {{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                            <h3 class="text-xl font-semibold text-white mt-2 mb-2">{{ $step['title'] ?? '' }}</h3>
                            <p class="text-slate-400">{{ $step['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
