<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    echo "Session email not found!";
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch bookings
$sql = "SELECT b.*, r.source, r.destination, br.departure_time, br.arrival_time, bi.bus_name, b.email AS booking_email,b.seat_number AS seat_number
        FROM bookings b
        JOIN routes r ON b.route_id = r.id
        JOIN bus_info bi ON b.bus_id = bi.id
        JOIN bus_routes br ON b.bus_id = br.bus_id AND b.route_id = br.route_id -- Corrected JOIN for bus_routes
        WHERE b.email = '$email'
        ORDER BY b.booking_time DESC";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Tickets</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(to right,#ff758c, #ff7eb3);
      color: #333;
      min-height: 100vh;
    }

    .btn-back {
      position: absolute;
      top: 20px;
      right: 20px;
      padding: 10px 20px;
      background-color: #a18cd1;
      color: white;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
      z-index: 999;
    }

    .btn-back:hover {
      background-color: #27ae60;
    }

    .container {
      background-color: white;
      padding: 30px;
      border-radius: 15px;
      max-width: 1000px;
      margin: 80px auto 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #3498db;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .view-btn {
      background-color: #8e44ad;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
    }

    .view-btn:hover {
      background-color: #732d91;
    }

    @media (max-width: 768px) {
      table {
        font-size: 14px;
      }
      th, td {
        padding: 10px;
      }
    }
  </style>
</head>
<body>

<a href="passenger_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>

<div class="container">
  <h2>🎟️ My Booked Tickets</h2>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Bus</th>
                <th>From → To</th>
                <th>Travel Date</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Fare</th>
                <th>Booking Time</th>
		<th>Email</th>
		<th>Seat Number</th>  
                <th>Ticket</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['bus_name'] ?></td>
                    <td><?= $row['source'] ?> → <?= $row['destination'] ?></td>
                    <td><?= $row['travel_date'] ?></td>
                    <td><?= $row['departure_time'] ?></td>
                    <td><?= $row['arrival_time'] ?></td>
                    <td>₹<?= $row['fare'] ?></td>
                    <td><?= $row['booking_time'] ?></td>
		    <td><?= $row['booking_email'] ?></td>
		    <td><?= $row['seat_number'] ?></td>
                    <td><a href="ticket.php?id=<?= $row['id'] ?>">View</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:#555;">No tickets booked yet.</p>
    <?php endif; ?>
</body>
</html>
