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

        /* Tooltips for flood project markers (force wrapping) */
        .leaflet-tooltip.floodproj-tooltip,
        .leaflet-tooltip.floodproj-tooltip .leaflet-tooltip-content {
            width: 200px !important;
            text-align: center;
            white-space: normal !important;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include_once('./include/header.php'); ?>
    <div id="map" class="w-full"></div>
    <?php include_once('./include/footer.php'); ?>


    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.vectorgrid/dist/Leaflet.VectorGrid.bundled.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <script>
        const map = L.map('map').setView([10, 118], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Make a new pane for project markers, above polygons
        map.createPane("floodProjPane");
        map.getPane("floodProjPane").style.zIndex = 650; // higher than overlayPane (polygon default 400–600)


        // Monkey patch for Leaflet >= 1.9
        if (!L.DomEvent.fakeStop) {
            L.DomEvent.fakeStop = function (e) {
                if (e.stopPropagation) e.stopPropagation();
                if (e.preventDefault) e.preventDefault();
                e.cancelBubble = true;
            };
        }

        /* --- globals --- */
        const floodLookup = {};   // {adm4_en: {floodVars: {"1":0,"2":0,"3":0 } } }
        const scoreLookup = {};   // {adm4_en: {score: 0.00, category: "Safe" } }

        /* --- helpers --- */
        function mapCategory(catRaw) {
            if (!catRaw && catRaw !== 0) return "Safe";
            const cat = String(catRaw).trim().toLowerCase();
            if (cat === "green" || cat === "safe") return "Safe";
            if (cat.includes("yellow") || cat.includes("low")) return "Low Risk";
            if (cat.includes("orange") || cat.includes("medium")) return "Medium Risk";
            if (cat.includes("red") || cat.includes("high")) return "High Risk";
            // default: title-case the raw
            return catRaw.charAt(0).toUpperCase() + catRaw.slice(1);
        }

        function getRiskColor(category) {
            switch (category) {
                case "Safe": return "green";
                case "Low Risk": return "gold";
                case "Medium Risk": return "darkorange";
                case "High Risk": return "red";
                default: return "gray";
            }
        }

        /* --- parse intersection_100year.csv (original pct_flood per Var) --- */
        function parseFloodCSV(text) {
            const parsed = Papa.parse(text, { header: true, skipEmptyLines: true }).data;
            parsed.forEach(row => {
                if (!row) return;
                const adm = String(row['adm4_en'] || row['ADM4_EN'] || "").trim();
                if (!adm) return;

                // Var might be "1", "2", "3"
                const varRaw = row['Var'] ?? row['var'] ?? row['VAR'] ?? "";
                const varClass = String(varRaw).trim();

                // Read original pct_flood (no summation) and round to 2 decimals
                const pctRaw = row['pct_flood'] ?? row['pct'] ?? 0;
                const pctNum = Number(pctRaw);
                const pct = isNaN(pctNum) ? 0 : +pctNum.toFixed(2);

                // Ensure entry exists
                if (!floodLookup[adm]) floodLookup[adm] = { floodVars: { "1": 0, "2": 0, "3": 0 } };

                // Store original pct for the Var (if varClass is numeric 1/2/3)
                if (["1", "2", "3"].includes(varClass)) {
                    floodLookup[adm].floodVars[varClass] = pct;
                } else {
                    // If Var contains extra text, try to extract a digit
                    const m = varClass.match(/[123]/);
                    if (m) floodLookup[adm].floodVars[m[0]] = pct;
                }
            });
        }

        /* --- parse flood_scores_labeled_kmeans.csv --- */
        function parseScoreCSV(text) {
            const parsed = Papa.parse(text, { header: true, skipEmptyLines: true }).data;
            parsed.forEach(row => {
                if (!row) return;
                const adm = String(row['adm4_en'] || row['ADM4_EN'] || "").trim();
                if (!adm) return;

                const scoreRaw = row['score_contrib'] ?? row['score'] ?? 0;
                const scoreNum = Number(scoreRaw);
                const score = isNaN(scoreNum) ? 0 : +scoreNum.toFixed(2);

                const catRaw = row['category'] ?? row['Category'] ?? "";
                const category = mapCategory(catRaw);

                scoreLookup[adm] = { score: score, category: category };
            });
        }

        /* --- Load both CSVs and GeoJSON, then render --- */
        Promise.all([
            fetch("./csv/intersection_100year.csv").then(r => r.text()),
            fetch("./csv/flood_scores_labeled_kmeans.csv").then(r => r.text()),
            fetch("./brgy/main.geojson").then(r => r.json())
        ]).then(([csvFloodText, csvScoresText, geojsonData]) => {
            // parse CSVs
            parseFloodCSV(csvFloodText);
            parseScoreCSV(csvScoresText);

            // --- Replace vectorGrid.slicer(...) with this L.geoJSON block ---
            const geojsonLayer = L.geoJSON(geojsonData, {
                // Use a canvas renderer for performance if you have many features:
                // renderer: L.canvas(),

                style: function (feature) {
                    const props = feature.properties || {};
                    const adm = (props.adm4_en || props.ADM4_EN || "").trim();
                    const scoreInfo = scoreLookup[adm] || { score: 0, category: "Safe" };

                    return {
                        color: "#333",                          // border
                        weight: 1,
                        fillColor: getRiskColor(scoreInfo.category), // fill based on category
                        fillOpacity: 0.6
                    };
                },

                onEachFeature: function (feature, layer) {
                    const props = feature.properties || {};
                    const adm = (props.adm4_en || props.ADM4_EN || "Unknown").trim();

                    const floodInfo = floodLookup[adm] || { floodVars: { "1": 0, "2": 0, "3": 0 } };
                    const scoreInfo = scoreLookup[adm] || { score: 0, category: "Safe" };

                    // Popup content (rounded values; score already rounded in parser)
                    const infoHtml = `
      <strong>${adm}</strong><br>
      Low: ${(floodInfo.floodVars?.["1"] ?? 0).toFixed(2)}%<br>
      Medium: ${(floodInfo.floodVars?.["2"] ?? 0).toFixed(2)}%<br>
      High: ${(floodInfo.floodVars?.["3"] ?? 0).toFixed(2)}%<br>
      <strong>Weigted Flood Index:</strong> ${Number(scoreInfo.score).toFixed(2)}<br>
      <strong>Category:</strong> ${scoreInfo.category}
    `;
                    layer.bindPopup(infoHtml, { autoPan: false });

                    // Hover behaviour
                    layer.on("mouseover", function (e) {
                        this.setStyle({
                            weight: 3,
                            color: "#000",
                            fillOpacity: 0.9
                        });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            this.bringToFront();
                        }
                        this.openPopup();
                    });

                    layer.on("mouseout", function (e) {
                        // reset to original style (calls style() above)
                        geojsonLayer.resetStyle(this);
                        this.closePopup();
                    });
                }
            }).addTo(map);

            // Fit map to layer bounds
            try {
                map.fitBounds(geojsonLayer.getBounds());
            } catch (err) {
                console.warn("Could not fit bounds:", err);
            }

        }).catch(err => {
            console.error("Error loading data:", err);
        });




        // ensure the floodProjPane exists (put this near map creation)
        if (!map.getPane("floodProjPane")) {
            map.createPane("floodProjPane");
            map.getPane("floodProjPane").style.zIndex = 650;
        }

        Papa.parse("./csv/flood_proj.csv", {
            download: true,
            header: true,
            complete: function (results) {
                results.data.forEach(obj => {
                    const name = (obj["name"] || "").trim();
                    const lat = parseFloat(obj["proj_latitude"]);
                    const lng = parseFloat(obj["proj_longitude"]);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        // content for popup (multi-line)
                        const popupHtml = `<strong>${name}</strong><br>Lat: ${lat.toFixed(5)}<br>Lng: ${lng.toFixed(5)}`;

                        const marker = L.circleMarker([lat, lng], {
                            pane: "floodProjPane",   // ensure on top
                            radius: 8,
                            fillColor: "blue",
                            color: "#fff",
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        });

                        // lightweight tooltip (hover) — add class so we can style it
                        marker.bindTooltip(name, {
                            direction: "top",
                            className: "floodproj-tooltip",
                            offset: [0, -10],
                            sticky: false
                        });

                        // robust popup (click) — maxWidth enforces wrapping nicely
                        marker.bindPopup(popupHtml, { maxWidth: 200, className: "floodproj-popup" });

                        marker.addTo(map);
                    }
                });
            }
        });




    </script>



</body>

</html>