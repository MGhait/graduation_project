@php
    $lat = $get('latitude') ?? 30.0444;
    $lng = $get('longitude') ?? 31.2357;
@endphp

<div>
    <div id="leaflet-map" style="height: 400px;"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const map = L.map('leaflet-map').setView([{{ $lat }}, {{ $lng }}], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([{{ $lat }}, {{ $lng }}], {draggable: true}).addTo(map);

            marker.on('dragend', function () {
                const position = marker.getLatLng();
            @this.set('data.latitude', position.lat)
                ;
            @this.set('data.longitude', position.lng)
                ;
            });
        });
    </script>

    @once
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endonce
</div>
