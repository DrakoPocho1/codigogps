<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = escape_data($_POST["api_key"]);

    if ($api_key == ESP32_API_KEY) {
        $latitude = escape_data($_POST["lat"]);
        $longitude = escape_data($_POST["lng"]);

        $sql = "INSERT INTO tbl_gps(lat,lng,created_date) 
                VALUES('".$latitude."','".$longitude."','".date("Y-m-d H:i:s")."')";

        if ($db->query($sql) === FALSE) {
            echo "Error: " . $sql . "<br>" . $db->error;
            exit();
        }
        
        echo "OK. INSERT ID: " . $db->insert_id;
        exit();
    } else {
        echo "Wrong API Key";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["get_data"])) {
    $sql = "SELECT * FROM tbl_gps ORDER BY created_date DESC LIMIT 1";
    $result = $db->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data = [
            'lat' => $row['lat'],
            'lng' => $row['lng'],
            'created_date' => $row['created_date']
        ];
        echo json_encode($data);
        exit();
    }
}

function escape_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        
        #container {
            max-width: 800px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            color: #333333;
            margin-bottom: 30px;
        }
        
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
        
        #gps-data {
            text-align: center;
            max-height: 200px;
            overflow-y: auto;
        }
        
        #clear-button {
            padding: 10px 20px;
            background-color: #ff0000;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }
        
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div id="container">
        <h1>APLICATIVO-GPS-SISTEMAS-UPAO</h1>
        <div id="map"></div>
        <div id="gps-data">
            <table id="data-table">
                <tr>
                    <th>Latitud</th>
                    <th>Longitud</th>
                    <th>Fecha y Hora</th>
                </tr>
            </table>
        </div>
        <button id="clear-button">Limpiar</button>
    </div>

    <script>
        function initMap() {
            var mapOptions = {
                center: { lat: 0, lng: 0 },
                zoom: 1
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            var marker;

            function updateMap() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        var data = JSON.parse(this.responseText);
                        if (marker) {
                            marker.setMap(null);
                        }
                        marker = new google.maps.Marker({
                            position: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) },
                            map: map,
                            title: 'Ubicación'
                        });
                        document.getElementById("data-table").innerHTML = "<tr><td>" + data.lat + "</td><td>" + data.lng + "</td><td>" + data.created_date + "</td></tr>";
                    }
                };
                xmlhttp.open("GET", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?get_data=true", true);
                xmlhttp.send();
            }

            setInterval(updateMap, 5000); // Actualizar cada 5 segundos

            document.getElementById("clear-button").addEventListener("click", function() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open("POST", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send("clear=true");

                // Actualizar la tabla de datos y reiniciar el marcador en el mapa
                document.getElementById("data-table").innerHTML = "";
                if (marker) {
                    marker.setMap(null);
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            initMap();
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</body>
</html>