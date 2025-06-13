@if ($getRecord()?->datasheet_file_id && $getRecord()->file)
    <div>
        <div class="text-pretty font-bold mb-2">{{ $getLabel() }}</div>
        <iframe
            src="{{ Storage::disk('public')->url($getRecord()->file->path) }}"
            width="100%"
            height="500"
            style="border: none;"
        ></iframe>
        <a
            href="{{ Storage::disk('public')->url($getRecord()->file->path) }}"
            target="_blank"
            class="mt-2 inline-flex items-center text-primary-600 hover:underline"
        >
            Download PDF
        </a>
    </div>
@else
    <p class="text-gray-500 italic">No datasheet available.</p>
@endif
