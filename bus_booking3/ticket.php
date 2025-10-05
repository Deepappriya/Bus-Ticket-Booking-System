<?php
include 'db.php';

$booking_id = $_GET['id'] ?? null;  // Get booking_id from URL

if ($booking_id) {
    $sql = "SELECT b.passenger_name, b.phone, b.fare, b.travel_date, b.booking_time,
            r.source, r.destination, br.departure_time, br.arrival_time,  -- Changed: Fetch from bus_routes
            bi.bus_name, t.seat_number
        FROM bookings b
        JOIN bus_info bi ON b.bus_id = bi.id
        JOIN routes r ON b.route_id = r.id
        JOIN bus_routes br ON br.route_id = r.id AND br.bus_id = bi.id  -- Added: Join bus_routes
        JOIN tickets t ON b.id = t.booking_id
        WHERE b.id = '$booking_id'
        LIMIT 1";

    echo "<br><br>\n";
if ($conn->error) {
    echo "\n";
}
$result = $conn->query($sql);
$num_rows = ($result) ? $result->num_rows : 0;
echo "\n";
if ($num_rows > 0 && isset($ticket)) {
    echo "\n";
}


    if (!$result) {
        echo "\n";  // Debug: Output MySQL error
        echo "<h3 style='color:red; text-align:center;'>❌ Database Error. Please contact support.</h3>";
        exit;
    }

    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        echo "\n"; // Debug: Indicate data was found
        echo "\n";
    } else {
        echo "\n";  // Debug: Indicate no data found
        echo "<h3 style='color:red; text-align:center;'>❌ Ticket not found or expired.</h3>";
        echo "<p style='text-align:center;'><a href='passenger_dashboard.php'>Go to Dashboard</a></p>";
        exit;
    }
} else {
    echo "\n";  // Debug: Indicate invalid ID
    echo "<h3 style='color:red; text-align:center;'>❌ Invalid Ticket ID.</h3>";
    echo "<p style='text-align:center;'><a href='passenger_dashboard.php'>Go to Dashboard</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Ticket</title>
    <meta http-equiv="refresh" content="15;url=success.php">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: left;
      padding: 20px;
      background-image: url('images/ticket1.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
    }

    .ticket-box {
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      width: 450px;
      max-width: 100%;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      text-align: left;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    p {
      margin: 8px 0;
      font-size: 15px;
      line-height: 1.5;
    }

    strong {
      color: #34495e;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      width: 100%;
      border: none;
      background-color: #27ae60;
      color: white;
      font-size: 16px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #219150;
    }

    @media print {
      body {
        background: white;
      }
      .ticket-box {
        box-shadow: none;
        border: 1px solid #ccc;
      }
      button {
        display: none;
      }
    }
    </style>
</head>
<body>

<div class="ticket-box">
    <h2>🎟️ Bus Ticket</h2>
    <?php if (isset($ticket)): ?>  <p><strong>Name:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?? 'N/A') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($ticket['phone'] ?? 'N/A') ?></p>
        <p><strong>Bus:</strong> <?= htmlspecialchars($ticket['bus_name'] ?? 'N/A') ?></p>
        <p><strong>From:</strong> <?= htmlspecialchars($ticket['source'] ?? 'N/A') ?> → <strong>To:</strong> <?= htmlspecialchars($ticket['destination'] ?? 'N/A') ?></p>
        <p><strong>Travel Date:</strong> <?= htmlspecialchars($ticket['travel_date'] ?? 'N/A') ?></p>
        <p><strong>Departure:</strong> <?= htmlspecialchars($ticket['departure_time'] ?? 'N/A') ?> | <strong>Arrival:</strong> <?= htmlspecialchars($ticket['arrival_time'] ?? 'N/A') ?></p>
        <p><strong>Booking Time:</strong> <?= htmlspecialchars($ticket['booking_time'] ?? 'N/A') ?></p>
        <p><strong>Fare:</strong> ₹<?= htmlspecialchars($ticket['fare'] ?? 'N/A') ?> /-</p>
        <p><strong>Seat Number:</strong> <?= htmlspecialchars($ticket['seat_number'] ?? 'N/A') ?></p>
        <br>
        <button onclick="window.print()">🖨️ Download Ticket</button>
        <br><br>
    <?php else: ?>
        <p>No ticket details to display.</p>
    <?php endif; ?>
</div>

</body>
</html>