@php
    $lat = $this->record->latitude ?? 0;
    $lng = $this->record->longtitude ?? 0;
@endphp


<div wire:ignore @if(!$lat || !$lng) class="mt-2 p-4 bg-gray-50 rounded text-gray-500" @else style="height: 400px;"
     @endif id="map"></div>

<script>
    const storeLat = {{ $lat }};
    const storeLng = {{ $lng }};


    if (!storeLng || !storeLat) {
        console.error('Invalid store coordinates');
        document.getElementById('map').innerHTML = '<p>Location not available</p>';
    } else {
        document.addEventListener('DOMContentLoaded', function () {

            console.log('Store Location:', storeLat, storeLng);

            //set the custom icons for stores and store's
            const storeIcon2 = L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });

            const storeIcon = L.icon({
                // iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });


            const map = L.map('map').setView([storeLat, storeLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            // Add marker for store
            L.marker([storeLat, storeLng], {icon: storeIcon})
                .addTo(map)
                .bindPopup('Store Location')
                .openPopup();

            // Fix map size issues (especially in tabs/panels)
            setTimeout(() => map.invalidateSize(), 200);
        });
    }
</script>
