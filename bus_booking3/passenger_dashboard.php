<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'passenger') {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passenger Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #f6d365, #fda085);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dashboard {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 400px;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }

    .btn:hover {
      background-color: #45a049;
    }

    .btn-logout {
      background-color: #e74c3c;
    }

    .btn-logout:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h2>🎉 Welcome, Passenger!</h2>

  <a class="btn" href="booking.php">🚌 Book Ticket</a>
  <a class="btn" href="view_tickets.php">🎟 View My Tickets</a>
  <a class="btn btn-logout" href="logout.php">🚪 Logout</a>
</div>

</body>
</html>