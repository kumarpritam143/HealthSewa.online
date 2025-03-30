<?php
include 'db.php';
$result = $conn->query("SELECT * FROM reports");

$data = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['disease'] . '_' . round($row['lat'], 3) . '_' . round($row['lng'], 3);
    if (!isset($data[$key])) {
        $data[$key] = [
            'disease' => $row['disease'],
            'lat' => $row['lat'],
            'lng' => $row['lng'],
            'count' => 1,
            'names' => [$row['name']]
        ];
    } else {
        $data[$key]['count'] += 1;
        $data[$key]['names'][] = $row['name'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Disease Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #007bff;
      color: white;
      padding: 15px 20px;
      text-align: center;
    }
    h2 {
      color: #333333;
      text-align: center;
      margin-top: 20px;
    }
    table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
      background: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    table th, table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #dddddd;
    }
    table th {
      background-color: #007bff;
      color: white;
    }
    table tr:hover {
      background-color: #f1f1f1;
    }
    #map {
      height: 500px;
      width: 90%;
      margin: 20px auto;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    footer {
      text-align: center;
      padding: 10px;
      background-color: #007bff;
      color: white;
      position: fixed;
      bottom: 0;
      width: 100%;
    }
  </style>
</head>
<body>
  <header>
    <h1>Disease Dashboard</h1>
  </header>
  <h2>Reported Cases</h2>
  <table>
    <thead>
      <tr>
        <th>Disease</th>
        <th>Location (Lat, Lng)</th>
        <th>Count</th>
        <th>Names</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['disease']) ?></td>
          <td>(<?= round($item['lat'], 3) ?>, <?= round($item['lng'], 3) ?>)</td>
          <td><?= $item['count'] ?></td>
          <td><?= implode(", ", $item['names']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div id="map"></div>



  <script>
    function initMap() {
      const center = { lat: 20.5937, lng: 78.9629 }; // India center
      const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: center
      });

      const data = <?= json_encode(array_values($data)) ?>;
      const diseaseColors = {
        "Dengue": "red",
        "Malaria": "green",
        "COVID-19": "blue",
        "Cholera": "orange",
        "Typhoid": "purple"
      };

      data.forEach(item => {
        const marker = new google.maps.Marker({
          position: { lat: parseFloat(item.lat), lng: parseFloat(item.lng) },
          map: map,
          title: `${item.disease} (${item.count})`,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10 + item.count,
            fillColor: diseaseColors[item.disease] || "gray",
            fillOpacity: 0.8,
            strokeWeight: 1,
          }
        });

        const info = new google.maps.InfoWindow({
          content: `<b>${item.disease}</b><br>Count: ${item.count}<br>Names: ${item.names.join(", ")}`
        });

        marker.addListener('click', () => {
          info.open(map, marker);
        });
      });
    }
  </script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC8gnhuaUtZ8pZLlNHJsPDXk0ySC3ssFCM&callback=initMap">
  </script>
</body>
</html>
