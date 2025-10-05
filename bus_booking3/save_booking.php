<?php
session_start();
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $travel_date = $_POST['travel_date'];
    $passenger_name = $_POST['passenger_name'];
    $phone = $_POST['phone'];
    $fare = $_POST['fare'];
    $seat_number = $_POST['seat_number'];
    $email = $_SESSION['email'];
    $booking_time = date("Y-m-d H:i:s");
     //$seatCheck = $conn->query("SELECT * FROM tickets WHERE route_id = '$route_id' AND seat_number = '$seat_number'");
     //if ($seatCheck->num_rows > 0) {
       //  echo "Seat already booked! Please go back and select another.";
         //exit();
     //}
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows == 0) {
        die("User not found.");
    }

    $user = $userResult->fetch_assoc();
    $user_id = $user['id'];

    // Step 3: Check if the seat is already booked for the same travel date
    $checkSeat = $conn->prepare("
    SELECT * 
    FROM bookings 
    WHERE bus_id = ? AND route_id = ? AND travel_date = ? AND seat_number = ?
");
$checkSeat->bind_param("iiss", $bus_id, $route_id, $travel_date, $seat_number);
$checkSeat->execute();
$seatResult = $checkSeat->get_result();

if ($seatResult->num_rows > 0) {
    echo "<script>alert('❌ Seat already booked! Please go back and select another.'); window.location.href='confirm_booking.php';</script>";
    exit();
}
    // Step 4: Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (passenger_name, phone, bus_id, route_id, travel_date, booking_time, fare, email, seat_number)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiissdss", $passenger_name, $phone, $bus_id, $route_id, $travel_date, $booking_time, $fare, $email, $seat_number);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        // Step 5: Optional - reduce available seats in bus_info
        $conn->query("UPDATE bus_info SET available_seats = available_seats - 1 WHERE id = '$bus_id'");

        // Step 6: Insert into passengers table if not exists
        $checkPassenger = $conn->query("SELECT * FROM passengers WHERE user_id = '$user_id'");
        if ($checkPassenger->num_rows == 0) {
            $conn->query("INSERT INTO passengers (user_id, phone, address) VALUES ('$user_id', '$phone', '')");
        }

        // Step 7: Insert into tickets table
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, route_id, seat_number, payment_status, booking_id)
                                VALUES (?, ?, ?, 'completed', ?)");
        $stmt->bind_param("iisi", $user_id, $route_id, $seat_number, $booking_id);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO seat_bookings (user_id, bus_id, route_id, seat_number, travel_date, booking_time)
                                VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiss", $user_id, $bus_id, $route_id, $seat_number, $travel_date);
        $stmt->execute();


        header("Location: ticket.php?id=" . $booking_id); // redirect to ticket page
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
