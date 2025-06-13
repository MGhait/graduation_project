{{-- resources/views/components/user-map-picker.blade.php --}}
{{--<div>--}}
{{--    <div id="leaflet-map" style="height: 400px;"></div>--}}

{{--    <script>--}}
{{--        document.addEventListener("DOMContentLoaded", function () {--}}
{{--            const map = L.map('leaflet-map').setView([{{ old('latitude', $get('latitude') ?? 30.0444) }}, {{ old('longitude', $get('longitude') ?? 31.2357) }}], 13);--}}

{{--            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--                attribution: '&copy; OpenStreetMap contributors'--}}
{{--            }).addTo(map);--}}

{{--            const marker = L.marker(map.getCenter(), {draggable: true}).addTo(map);--}}

{{--            marker.on('dragend', function () {--}}
{{--                const position = marker.getLatLng();--}}
{{--                document.querySelector('input[name="data.latitude"]').value = position.lat;--}}
{{--                document.querySelector('input[name="data.longitude"]').value = position.lng;--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}

{{--    --}}{{-- Load Leaflet CSS & JS only once --}}
{{--    @once--}}
{{--        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>--}}
{{--        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>--}}
{{--    @endonce--}}
{{--</div>--}}
{{--@php--}}
{{--    $lat = $get('latitude') ?? 30.0444;--}}
{{--    $lng = $get('longitude') ?? 31.2357;--}}
{{--@endphp--}}

{{--<div>--}}
{{--    <div id="leaflet-map" style="height: 400px;"></div>--}}

{{--    <script>--}}
{{--        document.addEventListener("DOMContentLoaded", function () {--}}
{{--            const map = L.map('leaflet-map').setView([{{ $lat }}, {{ $lng }}], 13);--}}

{{--            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--                attribution: '&copy; OpenStreetMap contributors'--}}
{{--            }).addTo(map);--}}

{{--            const marker = L.marker([{{ $lat }}, {{ $lng }}], {draggable: true}).addTo(map);--}}

{{--            marker.on('dragend', function () {--}}
{{--                const position = marker.getLatLng();--}}

{{--                // Filament's reactive way to update values--}}
{{--            @this.set('data.latitude', position.lat)--}}
{{--                ;--}}
{{--            @this.set('data.longitude', position.lng)--}}
{{--                ;--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}

{{--    @once--}}
{{--        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>--}}
{{--        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>--}}
{{--    @endonce--}}
{{--</div>--}}
@php
    $statePath = $getStatePath(); // full path like "data.map"
    $lat = data_get($getLivewire()->data, 'latitude', 30.0444);
    $lng = data_get($getLivewire()->data, 'longitude', 31.2357);
@endphp

<div wire:ignore style="height: 400px;" id="map"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map').setView([{{ $lat }}, {{ $lng }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([{{ $lat }}, {{ $lng }}], {draggable: true}).addTo(map);

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
        @this.set('{{ str_replace('.map', '', $statePath) }}.latitude', position.lat)
            ;
        @this.set('{{ str_replace('.map', '', $statePath) }}.longitude', position.lng)
            ;
        });
    });
</script>
