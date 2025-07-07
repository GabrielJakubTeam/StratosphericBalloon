<?php	
    $api_url = "http://127.0.0.1/get_data.php";
    $api_key = "API_KEY";

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
    <title>Lokalizator Balona PCZ</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        #map { width: 100%; height: 50vh; }
        h2 { text-align: center; }
        body { background-color: grey; }
        table { background-color: lightgrey; }
        #kontener {background-color: #f2f2f2; height: 200px; }
        .grupa { width: 25%; height: 100%; float: left; text-align: center; }
    </style>
</head>
<body>

<h2>Lokalizacja balonu</h2>

<div id="map"></div>

<h2>Dodatkowe dane</h2>

<div id="kontener">
    <div class="grupa">
        <h4>Godzina przesyłu danych</h4>
        <p id="dzien">Brak danych</p>
        <p id="godzina">Brak danych</p>
    </div>

    <div class="grupa">
        <h4>Wysokość n.p.m.</h4>
        <p id="wysokosc">Brak danych</p>
        <p>Jednostka: metr</p>
    </div>

    <div class="grupa">
        <h4>Prędkość</h4>
        <p id="predkosc">Brak danych</p>
        <p>Jednostka: km/h</p>
    </div>

    <div class="grupa">
        <h4>Kierunek ruchu</h4>
        <p id="kierunek">Brak danych</p>
        <p id="kierunekStr">Brak danych</p>
    </div>
</div>

<h2>Podgląd danych z bazy</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Długość, Szerokość, Kierunek</th>
        <th>Data, Wysokość, Prędkość</th>
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
        <tr><td colspan="3">Brak danych</td></tr>
    <?php endif; ?>
</table>

<script>
    function odczytajKierunek(stopnie) {
        if (stopnie >= 0 && stopnie < 45) {
            return 'Północ';
        } else if (stopnie >= 45 && stopnie < 135) {
            return 'Wschód';
        } else if (stopnie >= 135 && stopnie < 225) {
            return 'Południe';
        } else if (stopnie >= 225 && stopnie < 315) {
            return 'Zachód';
        } else {
            return 'Północ';
        }
    }

    let kierunek = odczytajKierunek(120); 
    var firstRow = document.querySelector("table tr:nth-child(2)"); 

    let uchwytData = document.getElementById("dzien");
    let uchwytGodzina = document.getElementById("godzina");
    let uchwytWysokosc = document.getElementById("wysokosc");
    let uchwytPredkosc = document.getElementById("predkosc");
    let uchwytKierunek = document.getElementById("kierunek");
    let uchwytKierunekStr = document.getElementById("kierunekStr");
    
    if (firstRow) {
        var firstId = firstRow.cells[0].textContent; 
        var firstLongitude = firstRow.cells[1].textContent; 
        var firstLatitude = firstRow.cells[2].textContent;  

	    var tab1 = firstLongitude.replace(/'/g, '"');
        tab1 = JSON.parse(tab1);

        var tab2 = firstLatitude.replace(/'/g, '"');	
        tab2 = JSON.parse(tab2);

        var currentLongitude = parseFloat(tab1[0]); 
        var currentLatitude = parseFloat(tab1[1]); 
	
        let godzinaStr = tab2[0].substring(8, 10);  
        let godzina = parseInt(godzinaStr, 10);
        godzina += 2; 

        let stopnie = parseInt(tab1[2]);
        let kierunek = odczytajKierunek(stopnie);

        uchwytGodzina.innerHTML = "Data: " + tab2[0].substring(6, 8) + "-" + tab2[0].substring(4, 6) + "-" + tab2[0].substring(0, 4);
        uchwytWysokosc.innerHTML = "Wysokość: " + tab2[1];
        uchwytPredkosc.innerHTML = "Prędkość: " + tab2[2];
        uchwytKierunek.innerHTML = "Stopnie: " + tab1[2];
        uchwytKierunekStr.innerHTML = "Kierunek: " + kierunek;
        uchwytData.innerHTML = "Godzina: " + godzina + ":" + tab2[0].substring(10, 12)+ ":" + tab2[0].substring(12, 14);
    } else {
        var currentLongitude = 0;
        var currentLatitude = 0;
    }

    var tileLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
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

    map.fitBounds(polyline.getBounds());
    L.marker( [currentLongitude, currentLatitude]).addTo(map);

	setTimeout(() => {
          location.reload();
        }, 20000);
</script>

</body>
</html>
