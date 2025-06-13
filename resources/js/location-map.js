export function initMap(mapData) {
    if (mapData.hasLocation) {
        const map = L.map('map').setView(
            [mapData.latitude, mapData.longitude],
            13
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([mapData.latitude, mapData.longitude])
            .addTo(map)
            .bindPopup('User Location')
            .openPopup();
    } else {
        document.getElementById('map').innerHTML = `
            <div class="p-4 text-center text-gray-500">
                No location data available
            </div>
        `;
    }
}
