<x-filament-panels::page>
    <div>
        <div id="map" style="height: 500px; width: 100%;"></div>

        @once
            @push('styles')
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
            @endpush

            @push('scripts')
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const mapData = {
                            latitude: @json($latitude),
                            longitude: @json($longitude),
                            hasLocation: @json($hasLocation),
                            name: @json($name)
                        };

                        if (!mapData.hasLocation) {
                            document.getElementById('map').innerHTML = `
                                <div class="p-4 text-center text-gray-500">
                                    No location data available
                                </div>
                            `;
                            return;
                        }

                        const map = L.map('map').setView(
                            [mapData.latitude, mapData.longitude],
                            13
                        );

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        L.marker([mapData.latitude, mapData.longitude])
                            .addTo(map)
                            .bindPopup(mapData.name) // this not shown
                            .openPopup();
                    });
                </script>
            @endpush
        @endonce
    </div>
</x-filament-panels::page>
