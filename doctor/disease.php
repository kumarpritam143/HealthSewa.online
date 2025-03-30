<!DOCTYPE html>
<html>
<head>
  <title>Disease Report Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #ffffff;
      padding: 20px 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    h2 {
      text-align: center;
      color: #333333;
    }
    label {
      display: block;
      margin: 10px 0 5px;
      color: #555555;
    }
    input[type="text"], input[type="number"], select, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #cccccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    input[type="submit"] {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }
    input[type="submit"]:hover {
      background-color: #0056b3;
    }
    textarea {
      resize: none;
      height: 80px;
    }
    .error {
      color: red;
      font-size: 0.9em;
      margin-top: -10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body onload="getLocation()">
  <div class="container">
      
    <h2>Report a Disease</h2>
    <form action="submit.php" method="POST" onsubmit="return validateLocation();">
      <label>Name:</label>
      <input type="text" name="name" required>

      <label>Age:</label>
      <input type="number" name="age" required>

      <label>Disease:</label>
      <select name="disease" required>
        <option value="">-- Select Disease --</option>
        <option value="Dengue">Dengue</option>
        <option value="Malaria">Malaria</option>
        <option value="COVID-19">COVID-19</option>
        <option value="Cholera">Cholera</option>
        <option value="Typhoid">Typhoid</option>
      </select>

      <label>Symptoms:</label>
      <textarea name="symptoms" required></textarea>

      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">

      <input type="submit" value="Submit Report">
    </form>
  </div>

  <script>
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          document.getElementById("lat").value = position.coords.latitude;
          document.getElementById("lng").value = position.coords.longitude;
        });
      } else {
        alert("Geolocation is not supported.");
      }
    }

    function validateLocation() {
      const lat = document.getElementById("lat").value;
      const lng = document.getElementById("lng").value;
      if (!lat || !lng) {
        alert("Please allow location access first.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
