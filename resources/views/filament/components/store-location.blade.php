@php
    $lat = $this->record->latitude ?? 0;
    $lng = $this->record->longtitude ?? 0;
@endphp

<div class="w-full">
    <div
        wire:ignore
        @if(!$lat || !$lng)
            class="mt-2 p-4 bg-gray-50 rounded text-gray-500"
        @else
            class="relative w-full h-96 rounded-lg overflow-hidden border border-gray-200"
        style="height: 400px; max-width: 100%; z-index: 1;"
        @endif
        id="map"
    ></div>
</div>

<style>
    /* Ensure the map container is properly constrained */
    #map {
        position: relative !important;
        z-index: 1 !important;
        max-width: 100% !important;
        overflow: hidden;
    }

    /* Fix Leaflet container z-index issues */
    #map .leaflet-container {
        z-index: 1 !important;
        position: relative !important;
    }

    /* Ensure map controls don't overflow */
    #map .leaflet-control-container {
        z-index: 2 !important;
    }

    /* Prevent map from breaking out of its container */
    #map .leaflet-map-pane {
        z-index: 1 !important;
    }

    /* Fix any popup z-index issues within the map */
    #map .leaflet-popup {
        z-index: 3 !important;
    }
</style>

<script>
    const storeLat = {{ $lat }};
    const storeLng = {{ $lng }};

    if (!storeLng || !storeLat) {
        console.error('Invalid store coordinates');
        document.getElementById('map').innerHTML = '<p class="text-center py-8 text-gray-500">Location not available</p>';
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Store Location:', storeLat, storeLng);

            // Set the custom icons for stores
            const storeIcon2 = L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });

            const storeIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });

            // Initialize map with proper options
            const map = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                boxZoom: true,
                keyboard: true,
                dragging: true,
                zIndex: 1
            }).setView([storeLat, storeLng], 15);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
                zIndex: 1
            }).addTo(map);

            // Add marker for store
            L.marker([storeLat, storeLng], {icon: storeIcon})
                .addTo(map)
                .bindPopup('Store Location')
                .openPopup();

            // Fix map size issues (especially in tabs/panels)
            setTimeout(() => {
                map.invalidateSize();
                // Ensure the map stays within its container
                const mapContainer = document.getElementById('map');
                if (mapContainer) {
                    mapContainer.style.position = 'relative';
                    mapContainer.style.zIndex = '1';
                }
            }, 300);

            // Additional fix for Filament panels
            setTimeout(() => {
                map.invalidateSize();
            }, 1000);
        });
    }
</script>
