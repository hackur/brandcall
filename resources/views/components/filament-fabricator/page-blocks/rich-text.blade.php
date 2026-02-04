@props(['heading', 'content', 'width', 'alignment'])

@php
    $maxWidth = match($width ?? 'default') {
        'narrow' => 'max-w-2xl',
        'wide' => 'max-w-7xl',
        default => 'max-w-4xl',
    };
    
    $textAlign = ($alignment ?? 'left') === 'center' ? 'text-center' : 'text-left';
@endphp

<section class="py-24 border-t border-slate-800/50">
    <div class="{{ $maxWidth }} mx-auto px-6 {{ $textAlign }}">
        @if($heading)
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-8">{{ $heading }}</h2>
        @endif
        
        @if($content)
            <div class="prose prose-invert prose-slate max-w-none 
                        prose-headings:text-white prose-headings:font-bold
                        prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-4
                        prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-3
                        prose-p:text-slate-400 prose-p:leading-relaxed
                        prose-a:text-brand-400 prose-a:no-underline hover:prose-a:text-brand-300
                        prose-strong:text-white
                        prose-ul:text-slate-400 prose-ol:text-slate-400
                        prose-li:my-1
                        prose-blockquote:border-brand-500 prose-blockquote:text-slate-300">
                {!! $content !!}
            </div>
        @endif
    </div>
</section>
