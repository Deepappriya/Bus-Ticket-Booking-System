<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passenger_name = $_POST['passenger_name'];
    $phone = $_POST['phone'];
    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $travel_date = $_POST['travel_date'];
    $fare = $_POST['fare'];
    $seat_number = $_POST['seat_number'];

    // Optional: Fetch bus and route info for display
    $sql = "SELECT r.source, r.destination, b.bus_name, br.departure_time, br.arrival_time
            FROM bus_routes br
            JOIN routes r ON br.route_id = r.id
            JOIN bus_info b ON br.bus_id = b.id
            WHERE br.route_id = '$route_id' AND b.id = '$bus_id'";

    $result = $conn->query($sql);
    $route = $result->fetch_assoc();
} else {
    echo "Invalid access.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px;
	    background-image: url('images/payment.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .card {
            background: white;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 25px;
        }

        .btn:hover {
            background: #219150;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>🧾 Review Your Booking</h2>
    <p><strong>Passenger Name:</strong> <?= htmlspecialchars($passenger_name) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
    <p><strong>Bus Name:</strong> <?= htmlspecialchars($route['bus_name']) ?></p>
    <p><strong>Route:</strong> <?= htmlspecialchars($route['source']) ?> → <?= htmlspecialchars($route['destination']) ?></p>
    <p><strong>Departure:</strong> <?= htmlspecialchars($route['departure_time']) ?> | <strong>Arrival:</strong> <?= htmlspecialchars($route['arrival_time']) ?></p>
    <p><strong>Date of Travel:</strong> <?= htmlspecialchars($travel_date) ?></p>
    <p><strong>Selected Seat:</strong> <?= htmlspecialchars($seat_number) ?></p>
    <p><strong>Fare:</strong> ₹<?= htmlspecialchars($fare) ?></p>

    <form method="POST" action="save_booking.php">
        <input type="hidden" name="passenger_name" value="<?= htmlspecialchars($passenger_name) ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
        <input type="hidden" name="bus_id" value="<?= htmlspecialchars($bus_id) ?>">
        <input type="hidden" name="route_id" value="<?= htmlspecialchars($route_id) ?>">
        <input type="hidden" name="travel_date" value="<?= htmlspecialchars($travel_date) ?>">
        <input type="hidden" name="seat_number" value="<?= htmlspecialchars($seat_number) ?>">
        <input type="hidden" name="fare" value="<?= htmlspecialchars($fare) ?>">

        <button type="submit" class="btn">✅ Proceed to Final Save</button>
    </form>
</div>

</body>
</html>
