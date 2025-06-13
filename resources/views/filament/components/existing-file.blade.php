{{--@if($url)--}}
{{--    <div>--}}
{{--        <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>--}}
{{--        <div class="mt-1">--}}
{{--            <a href="{{ $url }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">--}}
{{--                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"--}}
{{--                     xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>--}}
{{--                </svg>--}}
{{--                {{ basename($filename) }}--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}
@if($getState())
    <div>
        <div class="text-pretty font-bold mb-2">{{ $getLabel() }}</div>
        <iframe
            src="{{ Storage::disk('public')->url('files/' . $getState()) }}"
            class="w-full rounded-lg border border-gray-200"
            style="height: 500px;"
            frameborder="0"
        ></iframe>
        <a
            href="{{ Storage::disk('public')->url('files/' . $getState()) }}"
            target="_blank"
            class="mt-2 inline-flex items-center text-primary-600 hover:underline"
        >
            Download PDF
        </a>
    </div>
@else
    <div class="text-gray-500 italic">No datasheet available</div>
@endif
