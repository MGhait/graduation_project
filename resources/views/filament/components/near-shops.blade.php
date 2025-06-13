<div id="map" style="height: 400px;"></div>

<script>
    const userLat = {{ $user->latitude }};
    const userLng = {{ $user->longitude }};
    const stores = @json($stores); // $stores from the controller

    const map = L.map('map').setView([userLat, userLng], 13);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add user marker
    L.marker([userLat, userLng])
        .addTo(map)
        .bindPopup('You are here');

    // Add store markers
    stores.forEach(store => {
        L.marker([store.latitude, store.longitude])
            .addTo(map)
            .bindPopup(`<strong>${store.name}</strong><br>${store.distance.toFixed(2)} km away`);
    });
</script>
