<?php
session_start();
include 'db.php';

// Step 1: If it's a POST request, store data in session and redirect
// Step 1: Handle POST request (from booking.php)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['route_path']) && !empty($_POST['travel_date'])) {
        list($source, $destination) = explode('|', $_POST['route_path']);
        $_SESSION['source'] = $source;
        $_SESSION['destination'] = $destination;
        $_SESSION['travel_date'] = $_POST['travel_date'];
        
        header("Location: select_bus.php");
        exit();
    } else {
        die("Invalid form submission.");
    }
}

// Step 2: Use session values for query
$source = $_SESSION['source'] ?? null;
$destination = $_SESSION['destination'] ?? null;
$travel_date = $_SESSION['travel_date'] ?? null;

if ($source && $destination && $travel_date) {
//$travel_date_start = $travel_date . " 00:00:00";
$stmt = $conn->prepare("
        SELECT 
            bus_routes.id AS bus_route_id,
            bus_info.id AS bus_id,
            bus_info.bus_name,
            bus_info.total_seats,
            bus_info.available_seats,
            routes.id AS route_id,
            routes.source,
            routes.destination,
            bus_routes.departure_time,
            bus_routes.arrival_time,
            bus_routes.fare
        FROM bus_routes
        JOIN bus_info ON bus_routes.bus_id = bus_info.id
        JOIN routes ON bus_routes.route_id = routes.id
        WHERE routes.source = ? 
          AND routes.destination = ?
    ");
    $stmt->bind_param("ss", $source, $destination);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("SQL Error: " . $conn->error);
    }
} else {
    die("Invalid access. Please search for buses first.");
}
?>

<!DOCTYPE html>
<html>
<head>
   <title>Select Bus</title> 
   <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #43cea2, #185a9d);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
      background-image: url('images/busseat.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.97);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
      width: 500px;
      max-width: 90%;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    .bus-option {
      margin-bottom: 20px;
      padding: 15px;
      background: #f4f4f4;
      border: 1px solid #ddd;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      display: block;
  cursor: pointer;
  outline: none;
    }

    .bus-option:hover {
      background-color: #eaf6ff;
outline: none;
    }

    input[type="radio"] {
      margin-right: 10px;
      transform: scale(1.2);
    }

    .bus-details {
      font-size: 14px;
      color: #444;
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background-color: #2980b9;
      color: white;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #1f618d;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>🚌 Available Buses</h2>
	<form action="confirm_booking.php" method="POST">
	<!--<input type="hidden" name="route_id" value="<?= htmlspecialchars($route_id) ?>">-->
    <input type="hidden" name="travel_date" value="<?= htmlspecialchars($travel_date) ?>">
        <?php
	while ($bus = $result->fetch_assoc()) { ?>
    <label class="bus-option">
        <input type="radio" name="bus_id" value="<?= $bus['bus_id'] ?>" required>
        <input type="hidden" name="fare_<?= $bus['bus_id'] ?>" value="<?= $bus['fare'] ?>">
        <input type="hidden" name="route_id_<?= $bus['bus_id'] ?>" value="<?= $bus['route_id'] ?>">
        <div class="bus-details">
          <strong><?= $bus['bus_name'] ?></strong><br>
          From: <?= $bus['source'] ?> → <?= $bus['destination'] ?><br>
          Departure: <?= $bus['departure_time'] ?> <br> Fare: ₹<?= $bus['fare'] ?>
        </div>
      </label><?php } ?>
        <br>
      	<button type="submit">Proceed to Booking</button>
	</form>
</div>
<script>
  const form = document.querySelector("form");
  const radios = document.querySelectorAll('input[name="bus_id"]');

  radios.forEach(radio => {
    radio.addEventListener('change', () => {
      const busId = radio.value;
      const fare = document.querySelector(`input[name="fare_${busId}"]`).value;
      const routeId = document.querySelector(`input[name="route_id_${busId}"]`).value;

      // Remove existing dynamic hidden inputs
      const existingFare = form.querySelector('input[name="fare"]');
      const existingRoute = form.querySelector('input[name="route_id"]');
      if (existingFare) existingFare.remove();
      if (existingRoute) existingRoute.remove();

      // Add selected fare and route_id
      const fareInput = document.createElement("input");
      fareInput.type = "hidden";
      fareInput.name = "fare";
      fareInput.value = fare;
      form.appendChild(fareInput);

      const routeInput = document.createElement("input");
      routeInput.type = "hidden";
      routeInput.name = "route_id";
      routeInput.value = routeId;
      form.appendChild(routeInput);
    });
  });
</script>
</body>
</html>