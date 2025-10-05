<?php
session_start();
include 'db.php';

// Handle bus deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM bus_info WHERE id = $delete_id");
    header("Location: manage_buses.php");
    exit();
}

// Handle new bus addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_number = $_POST['bus_number'];
    $bus_name = $_POST['bus_name'];
    $total_seats = $_POST['total_seats'];

    $conn->query("INSERT INTO bus_info (bus_number, bus_name, total_seats, available_seats)
                  VALUES ('$bus_number', '$bus_name', $total_seats, $total_seats)");
    header("Location: manage_buses.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Buses</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right,#43cea2, #185a9d);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
	    background-image: url('images/manage_bus.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
	    margin-top:60px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
        }

        form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
            justify-content: center;
        }

        input[type="text"], input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 200px;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
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
            background-color: #f5f5f5;
            color: #333;
        }

        tr:hover {
            background-color: #f0f8ff;
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
    </style>
</head>
<body>
<a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    <div class="container">
        <h2>Manage Buses</h2>
        <form method="post" action="">
            <input type="text" name="bus_number" placeholder="Bus Number" required>
            <input type="text" name="bus_name" placeholder="Bus Name" required>
            <input type="number" name="total_seats" placeholder="Total Seats" required>
            <input type="submit" value="Add Bus">
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Bus Name</th>
                <th>Bus Number</th>
                <th>Total Seats</th>
                <th>Available Seats</th>
                <th>Actions</th>
            </tr>
    <?php
    $buses = $conn->query("SELECT * FROM bus_info");
$serial = 1; // Start serial number from 1
while ($bus = $buses->fetch_assoc()) {
    echo "<tr>
        <td>{$serial}</td>
        <td>{$bus['bus_name']}</td>
        <td>{$bus['bus_number']}</td>
        <td>{$bus['total_seats']}</td>
        <td>{$bus['available_seats']}</td>
        <td><a href='?delete_id={$bus['id']}' onclick='return confirm(\"Delete this bus?\")'>Delete</a></td>
    </tr>";
    $serial++; // Increase serial
}
    ?>
</table>
</div>
</body>
</html>
