<div class="border rounded-lg overflow-hidden">
    <iframe 
        src="{{ $getRecord()->getPreviewUrl() ?? $getRecord()->getDownloadUrl() }}" 
        class="w-full h-96"
        title="{{ $getRecord()->name }}"
    ></iframe>
</div>
<div class="mt-2 flex gap-2">
    <a 
        href="{{ $getRecord()->getDownloadUrl() }}" 
        target="_blank"
        class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-500"
    >
        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
        Download
    </a>
    <a 
        href="{{ $getRecord()->getPreviewUrl() ?? $getRecord()->getDownloadUrl() }}" 
        target="_blank"
        class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-500"
    >
        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
        Open in new tab
    </a>
</div>
