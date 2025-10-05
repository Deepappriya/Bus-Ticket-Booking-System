<?php
session_start();
include 'db.php';
// connect to DB
//$conn = new mysqli("localhost", "root", "", "bus_booking");

// check for errors
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//}

// Get input
//$source = $_GET['source'];
//$destination = $_GET['destination'];
//$date = $_GET['date']; // not used here unless you have date-specific routes
$source = isset($_GET['source']) ? $_GET['source'] : '';
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Query to search matching routes
$sql = "SELECT r.*, b.bus_name, b.total_seats, b.available_seats 
        FROM routes r
        JOIN bus_info b ON r.bus_id = b.id
        WHERE r.source = ? AND r.destination = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $source, $destination);
$stmt->execute();

$result = $stmt->get_result();

echo "<h2>Available Buses from $source to $destination</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $route_id = $row['id'];
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
        echo "<strong>Bus:</strong> " . $row['bus_name'] . "<br>";
        echo "<strong>Departure:</strong> " . $row['departure_time'] . "<br>";
        echo "<strong>Arrival:</strong> " . $row['arrival_time'] . "<br>";
        echo "<strong>Available Seats:</strong> " . $row['available_seats'] . "<br>";
        echo "<a href='confirm_booking.php?route_id=" . $row['id'] . "&travel_date=" . $_GET['date'] . "&source=" . urlencode($_GET['source']) . "&destination=" . urlencode($_GET['destination']) . "'>Book Now</a>";
        echo "</div>";
    }
} else {
    echo "No buses found for this route.";
}

$conn->close();
?>
