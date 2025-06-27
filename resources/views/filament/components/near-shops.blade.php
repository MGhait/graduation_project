@php use App\Models\Store; @endphp
{{--<div id="map" style="height: 400px;"></div>--}}

{{--<script>--}}
{{--    const userLat = {{ $user->latitude }};--}}
{{--    const userLng = {{ $user->longitude }};--}}
{{--    const stores = @json($stores); // $stores from the controller--}}

{{--    const map = L.map('map').setView([userLat, userLng], 13);--}}

{{--    // Add tile layer--}}
{{--    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--        attribution: '&copy; OpenStreetMap contributors'--}}
{{--    }).addTo(map);--}}

{{--    // Add user marker--}}
{{--    L.marker([userLat, userLng])--}}
{{--        .addTo(map)--}}
{{--        .bindPopup('You are here');--}}

{{--    // Add store markers--}}
{{--    stores.forEach(store => {--}}
{{--        L.marker([store.latitude, store.longitude])--}}
{{--            .addTo(map)--}}
{{--            .bindPopup(`<strong>${store.name}</strong><br>${store.distance.toFixed(2)} km away`);--}}
{{--    });--}}
{{--</script>--}}
{{--@php--}}
{{--    dump($this->record);--}}
{{--    dump(Store::getNearby($this->record->latitude,$this->record->longitude))--}}
{{--@endphp--}}

{{--<div id="map" style="height: 400px;"></div>--}}

{{--<script>--}}
{{--    const userLat = {{ $this->record->latitude ?? 0 }};--}}
{{--    const userLng = {{ $this->record->longitude ?? 0 }};--}}
{{--    const stores = @json($stores ?? []);--}}

{{--    const map = L.map('map').setView([userLat, userLng], 13);--}}

{{--    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--        attribution: '&copy; OpenStreetMap contributors'--}}
{{--    }).addTo(map);--}}

{{--    L.marker([userLat, userLng]).addTo(map).bindPopup('You are here');--}}

{{--    stores.forEach(store => {--}}
{{--        L.marker([store.latitude, store.longitude])--}}
{{--            .addTo(map)--}}
{{--            .bindPopup(`<strong>${store.name}</strong><br>${store.distance.toFixed(2)} km away`);--}}
{{--    });--}}
{{--</script>--}}
{{--@php--}}
{{--    $lat = $this->record->latitude ?? 0;--}}
{{--    $lng = $this->record->longitude ?? 0;--}}
{{--    $stores = Store::getNearby($lat, $lng);--}}
{{--    dump($stores)--}}
{{--@endphp--}}
{{--@json($stores)--}}
{{--<div wire:ignore style="height: 400px;" id="map"></div>--}}

{{--<script>--}}
{{--    document.addEventListener('DOMContentLoaded', function () {--}}
{{--        const userLat = {{ $lat }};--}}
{{--        const userLng = {{ $lng }};--}}
{{--        const stores = @json($stores);--}}

{{--        const map = L.map('map').setView([userLat, userLng], 15);--}}

{{--        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--            attribution: '&copy; OpenStreetMap contributors'--}}
{{--        }).addTo(map);--}}

{{--        // Add marker for user--}}
{{--        L.marker([userLat, userLng])--}}
{{--            .addTo(map)--}}
{{--            .bindPopup('User Location')--}}
{{--            .openPopup();--}}

{{--        // Add markers for stores--}}
{{--        stores.forEach(store => {--}}
{{--            L.marker([store.latitude, store.longtitude])--}}
{{--                .addTo(map)--}}
{{--                .bindPopup(`<strong>${store.name}</strong><br>${store.distance.toFixed(2)} km away`);--}}
{{--        });--}}

{{--        // Fix map size issues (especially in tabs/panels)--}}
{{--        setTimeout(() => map.invalidateSize(), 200);--}}
{{--    });--}}
{{--</script>--}}
@php
    $lat = $this->record->latitude ?? 0;
    $lng = $this->record->longitude ?? 0;
    $stores = Store::getNearby($lat, $lng);
@endphp

<!-- Add a simple store list below the map for better UX -->
<div class="mt-4">

    <h3 class="text-lg mt-3 font-medium">Nearby Stores ({{ count($stores) }})</h3>

    @if(count($stores) > 0)
        <div class="mt-2 space-y-2">
            @foreach($stores as $store)
                <div class="p-3 rounded shadow-sm">
                    <div class="flex justify-between">
                        <strong>{{ $store->name }}</strong>
                        <span class="text-blue-600">{{ number_format($store->distance, 2) }} km</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mt-2 p-4 bg-gray-50 rounded text-gray-500">
            No nearby stores found
        </div>
    @endif
</div>
<div wire:ignore
     @if(!$lat || !$lng)
         class="mt-2 p-4 bg-gray-50 rounded text-gray-500"
     @else
         class="relative w-full h-96 rounded-lg overflow-hidden border border-gray-200"
         style="height: 400px; max-width: 100%; z-index: 1;"
     @endif
     id="map">
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
    const userLat = {{ $lat }};
    const userLng = {{ $lng }};
    const stores = @json($stores);

    if(!userLng || !userLat){
        console.error('Invalid user coordinates');
        document.getElementById('map').innerHTML = '<p>Location not available</p>';
    }
    else {
        document.addEventListener('DOMContentLoaded', function () {

            console.log('User location:', userLat, userLng);
            console.log('Stores:', stores);

            //set the custom icons for stores and user's
            const userIcon2 = L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });

             const userIcon = L.divIcon({
        //          html: `<svg width="40px" height="40px" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
        //
        //     <defs>
        //
        //         <style>.cls-1{fill:#f0faff;}.cls-2{fill:#ff6096;}.cls-3{fill:#ffffff;}.cls-4{fill:#b3e5fc;}.cls-5,.cls-6{fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:4px;}.cls-6{stroke - dasharray:50 8 0.25 8 1000;}</style>
        //
        //     </defs>
        //
        //     <g data-name="09 pin" id="_09_pin">
        //
        //         <path class="cls-1" d="M103,124a19.88,19.88,0,0,1-8.81-2L61.81,106A20,20,0,0,0,33,124Z"/>
        //
        //         <path class="cls-2"
        //               d="M92.16,22.52A42.93,42.93,0,0,0,29.64,80.67l22.6,30.12h0l17.24,4.3a4,4,0,0,0,4.17-1.48L98.36,80.67A42.94,42.94,0,0,0,92.16,22.52Z"/>
        //
        //         <circle class="cls-3" cx="64" cy="55" r="32"/>
        //
        //         <polygon class="cls-4"
        //                  points="63.09 16.7 57.56 44.19 53.6 24.54 53.32 24.7 47.24 54.92 41.57 26.73 63.09 16.7"/>
        //
        //         <path class="cls-3"
        //               d="M105.15,27.85A49.14,49.14,0,0,0,30.8,16a50,50,0,0,0-8.37,9.4A9.85,9.85,0,0,0,24.77,38.8a2,2,0,0,0,3-.74,40.81,40.81,0,0,1,3.71-6.35,11.82,11.82,0,0,1,10.9-4.94,19,19,0,0,0,16.91-7,11.87,11.87,0,0,1,11.26-4.2A40,40,0,0,1,100.2,38a2,2,0,0,0,3.09.69A8.46,8.46,0,0,0,105.15,27.85Z"/>
        //
        //         <path class="cls-5"
        //               d="M103.62,38.39a43,43,0,0,1-5.26,42.28L73.65,113.61a4,4,0,0,1-4.17,1.48l-17.24-4.3L29.64,80.67a43,43,0,0,1-5.29-42.22M42.5,31.32A32,32,0,1,0,64,23c-.74,0-1.47,0-2.2.08M47.69,104.73A20,20,0,0,0,33,124h70a19.88,19.88,0,0,1-8.81-2l-19.58-9.63m-33-85.6,5.67,28.19L53.32,24.7l.28-.16,4,19.65L63.09,16.7"/>
        //
        //         <path class="cls-6"
        //               d="M105.15,27.85A49.14,49.14,0,0,0,30.8,16a50,50,0,0,0-8.37,9.4A9.85,9.85,0,0,0,24.77,38.8a2,2,0,0,0,3-.74,40.81,40.81,0,0,1,3.71-6.35,11.82,11.82,0,0,1,10.9-4.94,19,19,0,0,0,16.91-7,11.87,11.87,0,0,1,11.26-4.2A40,40,0,0,1,100.2,38a2,2,0,0,0,3.09.69A8.46,8.46,0,0,0,105.15,27.85Z"/>
        //
        //     </g>
        //
        // </svg>`,

                html: `<svg width="25" height="41" viewBox="0 0 25 41" xmlns="http://www.w3.org/2000/svg">
                 <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 12.5 12.5 28.5 12.5 28.5s12.5-16 12.5-28.5C25 5.6 19.4 0 12.5 0z" fill="#ff4444"/>
                 <circle cx="12.5" cy="12.5" r="4" fill="white"/>
               </svg>`,
                className: 'custom-div-icon',
                iconSize: [2, 1],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });

            const storeIcon = L.icon({
                // iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });


            const map = L.map('map').setView([userLat, userLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Add marker for user
            L.marker([userLat, userLng], {icon: userIcon})
                .addTo(map)
                .bindPopup('User Location')
                .openPopup();

            // Add markers for stores - NOTE THE SPELLING CORRECTION: longtitude → longitude
            stores.forEach(store => {
                // Check if we have valid coordinates
                if (store.latitude && store.longtitude) {
                    console.log(`Adding marker for ${store.name} at ${store.latitude}, ${store.longtitude}`);

                    L.marker([store.latitude, store.longtitude], {icon: storeIcon})
                        .addTo(map)
                        .bindPopup(`
                            <strong>${store.name}</strong><br>
                            ${store.distance.toFixed(2)} km away
                        `);
                } else {
                    console.warn(`Store ${store.name} has invalid coordinates:`, store);
                }
            });

            // Create bounds to fit all markers
            const bounds = L.latLngBounds([userLat, userLng]);
            stores.forEach(store => {
                if (store.latitude && store.longtitude) {
                    bounds.extend([store.latitude, store.longtitude]);
                }
            });

            // map.fitBounds(bounds, {padding: [50, 50]});

            // setTimeout(() => {
            //     map.setView([userLat, userLng], 15);
            // }, 300); // small delay to let fitBounds finish


            // L.circle([userLat, userLng], {
            //     color: '#ff4444',
            //     fillColor: '#ffcccc',
            //     fillOpacity: 0.3,
            //     radius: 20 // meters
            // }).addTo(map);

            // Fix map size issues (especially in tabs/panels)
            setTimeout(() => map.invalidateSize(), 200);
        });
    }
</script>



