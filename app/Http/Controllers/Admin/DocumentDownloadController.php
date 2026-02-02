<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(Document $document): StreamedResponse
    {
        // Check if file exists
        if (! Storage::disk('private')->exists($document->path)) {
            abort(404, 'Document not found');
        }

        return Storage::disk('private')->download(
            $document->path,
            $document->original_filename,
            ['Content-Type' => $document->mime_type]
        );
    }
}
