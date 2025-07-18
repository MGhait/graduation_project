<div id="mini-map" style="height: 300px; width: 100%;"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latitude = {{ $latitude ?? 'null' }};
        const longitude = {{ $longitude ?? 'null' }};

        if (latitude && longitude) {
            const map = L.map('mini-map').setView([latitude, longitude], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([latitude, longitude]).addTo(map)
                .bindPopup('Location')
                .openPopup();
        } else {
            document.getElementById('mini-map').innerHTML = '<p class="text-gray-500 text-center">No location available</p>';
        }
    });
</script>
