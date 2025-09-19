<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BgySubMuns Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 90vh;
        }
    </style>
</head>

<body class="bg-gray-100">
    <h1 class="text-center text-2xl font-bold py-4">Barangay Sub-Municipalities Map</h1>
    <div id="map" class="w-full"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.vectorgrid/dist/Leaflet.VectorGrid.bundled.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script>
        const map = L.map('map').setView([10, 118], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Monkey patch for Leaflet >= 1.9
        if (!L.DomEvent.fakeStop) {
            L.DomEvent.fakeStop = function (e) {
                if (e.stopPropagation) e.stopPropagation();
                if (e.preventDefault) e.preventDefault();
                e.cancelBubble = true;
            };
        }

        // --- Load CSV lookup (flood data) ---
        const floodLookup = {};
        const riskValues = [];

        function parseCSV(text) {
            const rows = text.trim().split("\n").map(r => r.split(","));
            const headers = rows[0];
            rows.slice(1).forEach(row => {
                const obj = {};
                headers.forEach((h, i) => obj[h] = row[i]);
                const adm = obj["adm4_en"];
                const varClass = obj["Var"];
                const pct = parseFloat(obj["pct_flood"]) || 0;

                if (!floodLookup[adm]) floodLookup[adm] = {};
                if (!floodLookup[adm].Max || pct > floodLookup[adm].Max) {
                    floodLookup[adm].Max = pct;
                }
                floodLookup[adm][varClass] = pct;
                riskValues.push(pct);
            });
        }

        // --- Compute color scale from quantiles ---
        let thresholds = [0, 0, 0, 0];
        function computeThresholds() {
            riskValues.sort((a, b) => a - b);
            const q = (p) => riskValues[Math.floor(p * (riskValues.length - 1))];
            thresholds = [q(0.2), q(0.4), q(0.6), q(0.8)];
        }

        function getRiskColor(val) {
            if (val <= thresholds[0]) return "green";
            if (val <= thresholds[1]) return "yellow";
            if (val <= thresholds[2]) return "orange";
            if (val <= thresholds[3]) return "red";
            return "darkred";
        }

        // --- Load CSV then GeoJSON ---
        Promise.all([
            fetch("./intersection_100year.csv").then(r => r.text()),
            fetch("./brgy/main.geojson").then(r => r.json())
        ]).then(([csvText, geojsonData]) => {
            parseCSV(csvText);
            computeThresholds();

            const vectorLayer = L.vectorGrid.slicer(geojsonData, {
                rendererFactory: L.canvas.tile,
                vectorTileLayerStyles: {
                    sliced: function (props) {
                        const adm = props.adm4_en;
                        const floodInfo = floodLookup[adm] || {};
                        const riskPct = floodInfo.Max || 0;
                        const color = getRiskColor(riskPct);

                        return {
                            fillColor: color,
                            fillOpacity: 0.5,
                            color: "#333",
                            weight: 1
                        };
                    }
                },
                interactive: true
            }).addTo(map);

            // Hover events
            vectorLayer.on("mouseover", function (e) {
                const props = e.layer.properties || {};
                const adm = props.adm4_en || "Unknown";
                const floodInfo = floodLookup[adm] || {};

                const infoHtml = `
                <strong>${adm}</strong><br>
                Low Flood: ${floodInfo["1"] ?? "N/A"}%<br>
                Medium Flood: ${floodInfo["2"] ?? "N/A"}%<br>
                High Flood: ${floodInfo["3"] ?? "N/A"}%<br>
                <strong>Max Risk:</strong> ${floodInfo.Max ?? 0}%
            `;

                L.popup({ autoPan: false })
                    .setLatLng(e.latlng)
                    .setContent(infoHtml)
                    .openOn(map);

                e.layer.setStyle({
                    weight: 3,
                    color: "#000",
                    fillOpacity: 0.8
                });
            });

            vectorLayer.on("mouseout", function (e) {
                const props = e.layer.properties || {};
                const adm = props.adm4_en || "Unknown";
                const floodInfo = floodLookup[adm] || {};
                const riskPct = floodInfo.Max || 0;
                const color = getRiskColor(riskPct);

                e.layer.setStyle({
                    fillColor: color,
                    fillOpacity: 0.5,
                    color: "#333",
                    weight: 1
                });

                map.closePopup();
            });

            // Fit to bounds
            const bounds = L.geoJSON(geojsonData).getBounds();
            map.fitBounds(bounds);
        });
    </script>
    


</body>

</html>