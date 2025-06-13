{{--<x-filament-widgets::widget>--}}
{{--    <x-filament::section>--}}
{{--        --}}{{-- Widget content --}}
{{--    </x-filament::section>--}}
{{--</x-filament-widgets::widget>--}}
<div>
    @if (!$user || !$user->latitude || !$user->longitude)
        <p class="text-sm text-gray-500">No location data available.</p>
    @else
        <div id="map" style="height: 400px;" class="rounded-xl border border-gray-300"></div>

        @once
            @push('styles')
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
            @endpush

            @push('scripts')
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const lat = {{ $user->latitude }};
                        const lng = {{ $user->longitude }};
                        const map = L.map('map').setView([lat, lng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        L.marker([lat, lng]).addTo(map).bindPopup("{{ $user->name ?? 'User Location' }}").openPopup();
                    });
                </script>
            @endpush
        @endonce
    @endif
</div>
