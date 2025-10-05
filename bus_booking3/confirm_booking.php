<?php
session_start();
include 'db.php';

// Step 1: If POST request, store values in session and redirect
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['route_id'] = $_POST['route_id'];
    $_SESSION['selected_bus_id'] = $_POST['bus_id'];
    $_SESSION['travel_date'] = $_POST['travel_date'];
    $_SESSION['fare'] = $_POST['fare'];

    header("Location: confirm_booking.php");
    exit();
}

// Step 2: On redirected GET request, fetch from session
$route_id = $_SESSION['route_id'] ?? null;
$bus_id = $_SESSION['selected_bus_id'] ?? null;
$travel_date = $_SESSION['travel_date'] ?? null;
$fare = $_SESSION['fare'] ?? null;
if ($route_id && $bus_id && $travel_date && $fare) {
    $sql = "SELECT br.*, r.source, r.destination, b.bus_name, b.total_seats AS total_seats, b.id AS bus_id
            FROM bus_routes br
            JOIN routes r ON br.route_id = r.id
            JOIN bus_info b ON br.bus_id = b.id
            WHERE br.route_id = '$route_id' AND b.id = '$bus_id'";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $route = $result->fetch_assoc();
    } else {
        die("No route found. Please go back and try again.");
    }
} else {
    die("Invalid access. Please search for buses first.");
}

$bookedSeats = [];
$seatQuery = "SELECT seat_number FROM seat_bookings 
              WHERE bus_id = '$bus_id' AND route_id = '$route_id'AND travel_date = '$travel_date'";
$seatResult = $conn->query($seatQuery);

if ($seatResult && $seatResult->num_rows > 0) {
    while ($row = $seatResult->fetch_assoc()) {
        $bookedSeats[] = $row['seat_number'];
    }
}
$totalSeats = (int)$route['total_seats'];
$bookedCount = count($bookedSeats);
$availableSeats = $totalSeats - $bookedCount;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #74ebd5, #acb6e5);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: right;
      background-image: url('images/ticketbook.jpg'); /* Optional image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
      background-attachment: fixed;
    }

    .container {
      background-color: white;
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 90%;
      float:right;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    p {
      margin: 8px 0;
      font-size: 15px;
    }

    form {
      margin-top: 20px;
    }

    label {
      display: block;
      margin-bottom: 12px;
      color: #333;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      background-color: #2980b9;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #1f618d;
    }
.seat-map {
  display: flex;
  flex-direction: column;
  margin: 15px 0;
}

.seat-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.seat {
  display: inline-block;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-right: 5px;
  background-color: #e0f7fa;
  cursor: pointer;
}

.seat input {
  display: none;
}

.seat:hover {
  background-color: #b2ebf2;
}

.seat input:checked + label {
  background-color: #00acc1;
  color: white;
}

.seat.booked {
  background-color: #ccc;
  cursor: not-allowed;
  color: #666;
}
  </style>
</head>
<body>
 
<div class="container">
  <h2>🎫 Confirm Your Ticket</h2>
    <p><strong>Bus:</strong> <?= $route['bus_name'] ?></p>
    <p><strong>From:</strong> <?= $route['source'] ?> → <strong>To:</strong> <?= $route['destination'] ?></p>
    <p><strong>Departure:</strong> <?= $route['departure_time'] ?> | <strong>Arrival:</strong> <?= $route['arrival_time'] ?></p>
    <p><strong>Date of Travel:</strong> <?= $travel_date ?></p>
    <p><strong>Fare:</strong> ₹<?= $fare ?></p>
    <p><strong>Total Seats:</strong> <?= $totalSeats ?></p>
<p><strong>Booked Seats:</strong> <?= $bookedCount ?></p>
<p><strong>Available Seats:</strong> <?= $availableSeats ?></p>

    <form method="POST" action="payment.php">
    <input type="hidden" name="bus_id" value="<?= $route['bus_id'] ?>">
    <input type="hidden" name="route_id" value="<?= $route_id ?>">
    <input type="hidden" name="travel_date" value="<?= $travel_date ?>">
    <input type="hidden" name="fare" value="<?= $fare ?>">
	

    <label>Passenger Name: <input type="text" name="passenger_name" required></label>
    <label>Phone: <input type="text" name="phone" required></label>

    <p><strong>Select Your Seat:</strong></p>
    <div class="seat-map">
        <?php
            $totalSeats = (int)$route['total_seats'];
            $cols = 4;
            $rows = ceil($totalSeats / $cols);
            $seatNumber = 1;

            for ($i = 0; $i < $rows; $i++) {
                echo "<div class='seat-row'>";
                for ($j = 0; $j < $cols; $j++) {
                    if ($seatNumber > $totalSeats) break;
                    $seatLabel = "S" . $seatNumber;
                    $isBooked = in_array($seatLabel, $bookedSeats);
                    $disabled = $isBooked ? "disabled" : "";
                    $class = $isBooked ? "seat booked" : "seat";
                    echo "<div class='$class'>";
                    echo "<input type='radio' id='$seatLabel' name='seat_number' value='$seatLabel' required $disabled>";
                    echo "<label for='$seatLabel'>$seatLabel</label>";
                    echo "</div>";
                    $seatNumber++;
                }
                echo "</div>";
            }
            ?>
    </div>
<!--<div style="display: flex; flex-wrap: wrap; gap: 10px;">
    <?php
    for ($i = 1; $i <= $totalSeats; $i++) {
        $seatNumber = "S" . $i;
        echo "<label style='width: 60px; text-align: center;'>
                <input type='checkbox' name='seats[]' value='$seatNumber'> $seatNumber
              </label>";
    }
    ?>
</div>-->
    <button type="submit">✅ Confirm & Pay</button>
</form>
</div>
</body>
</html>
