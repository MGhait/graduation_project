@if($getState())
    <div>
        <div class=" text-pretty bold mb-3">{{ $getLabel() }}</div>
{{--        <img src="{{ asset('/storage/images/' . $getState()) }}"--}}
        <img src="{{ Storage::disk('public')->url('images/' . $getState()) }}"
             alt="nn"
             height="50px"
             class="rounded-lg object-contain mt-3"
             style="height: 350px; max-height: 400px"
        />
    </div>
@else
    <div class="text-gray-500 italic">No image available</div>
@endif

{{--@php--}}
{{--    $imageUrl = $getState();--}}
{{--    $fullImagePath = null;--}}

{{--    if ($imageUrl) {--}}
{{--        // Handle different possible URL formats--}}
{{--        if (str_starts_with($imageUrl, 'http')) {--}}
{{--            // Full URL already--}}
{{--            $fullImagePath = $imageUrl;--}}
{{--        } elseif (str_starts_with($imageUrl, '/storage/')) {--}}
{{--            // Already has /storage/ prefix--}}
{{--            $fullImagePath = asset($imageUrl);--}}
{{--        } elseif (str_starts_with($imageUrl, 'storage/')) {--}}
{{--            // Has storage/ prefix but missing leading slash--}}
{{--            $fullImagePath = asset('/' . $imageUrl);--}}
{{--        } else {--}}
{{--            // Just the filename--}}
{{--            $fullImagePath = asset('/storage/images/' . $imageUrl);--}}
{{--        }--}}
{{--    }--}}

{{--    // Debug output - remove this after fixing--}}
{{--//    \Log::info('Blade template - Raw state: ' . ($imageUrl ?? 'null'));--}}
{{--//    \Log::info('Blade template - Full path: ' . ($fullImagePath ?? 'null'));--}}
{{--@endphp--}}
{{--@if($imageUrl && $fullImagePath)--}}
{{--    <div>--}}
{{--        <div class="text-pretty font-bold">{{ $getLabel() }}</div>--}}
{{--        <div class="mt-2">--}}
{{--            <img src="{{ $fullImagePath }}"--}}
{{--                 alt="{{ $getLabel() }}"--}}
{{--                 class="rounded-lg object-contain mt-1 border border-gray-200"--}}
{{--                 style="height: 350px; max-height: 400px; width: auto;"--}}
{{--                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"--}}
{{--            />--}}
{{--            <div class="text-red-500 italic hidden">--}}
{{--                Failed to load image: {{ $fullImagePath }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- Debug info - remove after fixing --}}
{{--        <div class="text-xs text-gray-400 mt-1">--}}
{{--            Debug: {{ $fullImagePath }}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@else--}}
{{--    <div class="text-gray-500 italic">--}}
{{--        No image available--}}
{{--        @if($imageUrl)--}}
{{--            <br><small class="text-xs">State: {{ $imageUrl }}</small>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--@endif--}}
