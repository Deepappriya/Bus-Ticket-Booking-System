<?php
session_start();
include 'db.php';

// Delete a route if requested
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("DELETE FROM bus_routes WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_routes.php");
    exit();
}

// Insert a new route
// Insert or get route ID
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $fare = intval($_POST['fare']);
    $bus_id = intval($_POST['bus_id']);
$departure_time =  $_POST['departure_time'];
$arrival_time =  $_POST['arrival_time'];

    // Check if route already exists
    $route_check = $conn->prepare("SELECT id FROM routes WHERE source=? AND destination=? ");
    $route_check->bind_param("ss", $source, $destination);
    $route_check->execute();
    $route_check->store_result();

    if ($route_check->num_rows > 0) {
        $route_check->bind_result($route_id);
        $route_check->fetch();
    } else {
        // Insert new route
        $insert_route = $conn->prepare("INSERT INTO routes (source, destination) VALUES (?, ?)");
        $insert_route->bind_param("ss", $source, $destination);
        $insert_route->execute();
        $route_id = $insert_route->insert_id;
        $insert_route->close();
    }
    $route_check->close();

    // Insert into bus_routes
    // Check if the same bus is already assigned to this route
$bus_route_check = $conn->prepare("SELECT id FROM bus_routes WHERE bus_id = ? AND route_id = ?");
$bus_route_check->bind_param("ii", $bus_id, $route_id);
$bus_route_check->execute();
$bus_route_check->store_result();

if ($bus_route_check->num_rows == 0) {
    // Insert only if this bus hasn't been assigned to this route
    $insert_bus_route = $conn->prepare("INSERT INTO bus_routes (bus_id, route_id, departure_time, arrival_time,fare) VALUES (?, ?, ?, ?, ?)");
    $insert_bus_route->bind_param("iissi", $bus_id, $route_id, $departure_time, $arrival_time,$fare);
    $insert_bus_route->execute();
    $insert_bus_route->close();
}
$bus_route_check->close();


    header("Location: manage_routes.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Routes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
	    background-image: url('images/routes.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.97);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            max-width: 1000px;
            width: 100%;
	    margin-top:60px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 200px;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #1f618d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
.btn-back {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 10px 20px;
  background-color: #2ecc71;
  color: white;
  border: none;
  border-radius: 8px;
  text-align: center;
  text-decoration: none;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
  z-index: 1000;
}

.btn-back:hover {
  background-color: #27ae60;
}

        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: center;
            }

            input, select {
                width: 90%;
            }
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
<br><br>
<div class="container">
<h2>🛣 Add New Route</h2>
<form method="POST">
    <label>Bus:</label><br>
    <select name="bus_id" required>
        <option value="">Select Bus</option>
        <?php
        $buses = $conn->query("SELECT id, bus_name FROM bus_info");
        while ($bus = $buses->fetch_assoc()) {
            echo "<option value='{$bus['id']}'>{$bus['bus_name']}</option>";
        }
        ?>
    </select><br>

    <label>Source:</label><br>
    <input type="text" name="source" required><br>

    <label>Destination:</label><br>
    <input type="text" name="destination" required><br>
    
    <label>Departure Time:</label><br>
    <input type="time" name="departure_time" required><br>
    <label>Arrival Time:</label>
    <input type="time" name="arrival_time" required>

    <div style="width:100%; padding-left:350px;">

 <label >          Fare (₹):</label>
  <input type="number" name="fare" required style="width:25%; "><br>
</div>
  <button type="submit">➕ Add Route</button>
</form>

<hr>

<h2>📋 Existing Routes</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Bus Name</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Departure</th>
        <th>Arrival</th>
        <th>Action</th>
        <th>Fare (₹)</th>  
    </tr>

    <?php
    $routes = $conn->query("
    SELECT br.id AS br_id, b.bus_name, r.source, r.destination, br.departure_time, br.arrival_time, br.fare
    FROM bus_routes br
    JOIN bus_info b ON br.bus_id = b.id
    JOIN routes r ON br.route_id = r.id");
$serial = 1; // Start serial number from 1
while ($route = $routes->fetch_assoc()) {
    echo "<tr>
        <td>{$serial}</td>
        <td>{$route['bus_name']}</td>
        <td>{$route['source']}</td>
        <td>{$route['destination']}</td>
        <td>{$route['departure_time']}</td>
        <td>{$route['arrival_time']}</td>
        <td>₹{$route['fare']}</td>
        <td><a class='delete-btn' href='?delete_id={$route['br_id']}' onclick='return confirm(\"Delete this route?\")'>Delete</a></td>
    </tr>";
    $serial++; // Increment after each route
}
    ?>
</table> 
</div>
</body>
</html>
 
