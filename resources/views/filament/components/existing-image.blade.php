{{--@if(is_callable($url))--}}
{{--    @php--}}
{{--        $imageUrl = $url($record ?? null);--}}
{{--    @endphp--}}
{{--    @if($imageUrl)--}}
{{--        <div>--}}
{{--            {{ $getRecord()->url }}--}}
{{--            <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>--}}
{{--            <div class="mt-1">--}}
{{--                <img src="{{ $imageUrl }}" alt="{{ $label }}" class="max-h-48 rounded-lg border border-gray-300">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--@elseif($url)--}}
{{--    <div>--}}
{{--            {{ $getRecord() }}--}}
{{--        <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>--}}
{{--        <div class="mt-1">--}}
{{--            <img src="{{ $url }}" alt="{{ $label }}" class="max-h-48 rounded-lg border border-gray-300">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}
@if($url)
    <div>
        <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>
        <div class="mt-1">
            <img src="{{ $url }}" alt="{{ $label }}" class="max-h-48 rounded-lg border border-gray-300">
        </div>
    </div>
@endif
