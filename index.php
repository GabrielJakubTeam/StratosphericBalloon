<?php
    // this code makes a request to an external api
    $api_url = "http://ipv4/BaloonnPage/get_data.php";
    $api_key = "Api_key";

    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Bearer " . $api_key
        ]
    ]);

    $data = file_get_contents($api_url, false, $context);
    $data_array = json_decode($data, true);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data list</title>

    <!-- include leaflet open street map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        #map { width: 100%; height: 60vh; }
        h2 { text-align: center; }
        body { background-color: grey; }
        table { background-color: lightgrey; }
    </style>
</head>
<body>
    <h2>Map</h2>
    <div id="map"></div>
    <h2>Data from database</h2>

    <!-- table for storing coordinates -->
    <table>
        <tr>
            <th>ID</th>
            <th>Longitude</th>
            <th>Latitude</th>
        </tr>
        <?php if (!isset($data_array["error"])): ?>
            <?php foreach ($data_array as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= htmlspecialchars($row["longitude"]) ?></td>
                    <td><?= htmlspecialchars($row["latitude"]) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No data</td></tr>
        <?php endif; ?>
    </table>

    <script>
        var firstRow = document.querySelector("table tr:nth-child(2)"); 
        
        // data validation
        if (firstRow) {
            var firstId = firstRow.cells[0].textContent; 
            var firstLongitude = firstRow.cells[1].textContent; 
            var firstLatitude = firstRow.cells[2].textContent;  

            console.log("First ID: " + firstId);
            console.log("First Longitude: " + firstLongitude);
            console.log("First Latitude: " + firstLatitude);

            var currentLongitude = parseFloat(firstLongitude); 
            var currentLatitude = parseFloat(firstLatitude); 
        } else {
            var currentLongitude = 0;
            var currentLatitude = 0;
        }

        // displaying an interactive map
        var tileLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',
        {
            attribution: false
        });

        var map = L.map('map',
        {
            zoomControl: true,
            layers: [tileLayer],
            maxZoom: 18,
            minZoom: 6
        })
        .setView([43.64701, -79.39425], 15);

            setTimeout(function () { map.invalidateSize() }, 800);
            var latlngs = [
                [currentLongitude, currentLatitude]
            ];

            var polyline = L.polyline(latlngs, {color: 'red'}).addTo(map);

            // zoom the map to the polyline
            map.fitBounds(polyline.getBounds());
            L.marker( [currentLongitude, currentLatitude]).addTo(map);
    </script>

</body>
</html>
