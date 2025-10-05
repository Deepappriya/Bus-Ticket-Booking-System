<?php 
include 'db.php';
$result = $conn->query("SELECT DISTINCT source, destination FROM routes");
//$sources = $conn->query("SELECT DISTINCT source FROM routes");
//$destinations = $conn->query("SELECT DISTINCT destination FROM routes");
//$result = $conn->query("//SELECT br.id AS br_id, r.source, r.destination, br.departure_time, b.bus_name
    //FROM bus_routes br
    //JOIN routes r ON br.route_id = r.id
    //JOIN bus_info b ON br.bus_id = b.id
//");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Your Bus</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #00c9ff, #92fe9d);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content:center;
	    background-image: url('images/busroute2.jfif'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        select, input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 5px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            font-weight: bold;
            margin-top: 25px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #1f618d;
        }

        option {
            padding: 5px;
        }

        @media (max-width: 500px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🚌 Book Your Bus</h2>


     <form action="select_bus.php" method="POST">
        <label>Choose Route:</label>
        <select name="route_path" required>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $value = $row['source'] . "|" . $row['destination'];
                    $label = $row['source'] . " → " . $row['destination'];
                    echo "<option value='$value'>$label</option>";
                }
            } else {
                echo "<option disabled>No routes available</option>";
            }
            ?>
        </select><br><br>

        <label>Travel Date:</label>
        <input type="date" name="travel_date" required><br><br>

        <input type="submit" value="Search Buses">
    </form>
</div>
</body>
</html>
