<div id="mini-map" style="height: 300px; width: 100%;"></div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latitudeInput = document.querySelector('input[name="latitude"]');
            const longitudeInput = document.querySelector('input[name="longitude"]');

            let latitude = parseFloat(latitudeInput.value) || 31.0322222; // Default latitude
            let longitude = parseFloat(longitudeInput.value) || 31.3956389; // Default longitude

            const map = L.map('mini-map').setView([latitude, longitude], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let marker = L.marker([latitude, longitude], {draggable: true}).addTo(map);

            // Update inputs when marker is dragged
            marker.on('dragend', function (event) {
                const newLatLng = event.target.getLatLng();
                latitudeInput.value = newLatLng.lat.toFixed(6);
                longitudeInput.value = newLatLng.lng.toFixed(6);
            });

            // Place a new marker when the map is clicked
            map.on('click', function (event) {
                const newLatLng = event.latlng;
                marker.setLatLng(newLatLng);
                latitudeInput.value = newLatLng.lat.toFixed(6);
                longitudeInput.value = newLatLng.lng.toFixed(6);
            });

            // Update marker position when inputs change
            latitudeInput.addEventListener('input', function () {
                const newLat = parseFloat(latitudeInput.value);
                const newLng = parseFloat(longitudeInput.value);
                if (!isNaN(newLat) && !isNaN(newLng)) {
                    marker.setLatLng([newLat, newLng]);
                    map.setView([newLat, newLng], map.getZoom());
                }
            });

            longitudeInput.addEventListener('input', function () {
                const newLat = parseFloat(latitudeInput.value);
                const newLng = parseFloat(longitudeInput.value);
                if (!isNaN(newLat) && !isNaN(newLng)) {
                    marker.setLatLng([newLat, newLng]);
                    map.setView([newLat, newLng], map.getZoom());
                }
            });
        });
    </script>
@endpush
