<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #667eea, #764ba2);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dashboard {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      text-align: center;
      width: 400px;
    }

    h2 {
      margin-bottom: 25px;
      color: #2c3e50;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      background-color: #2980b9;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #1f618d;
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
  <h2>🛠 Admin Dashboard</h2>

  <a class="btn" href="manage_buses.php">🚌 Manage Buses</a>
  <a class="btn" href="manage_routes.php">🗺️ Manage Routes</a>
  <a class="btn" href="view_bookings.php">📋 View Bookings</a>
  <a class="btn btn-logout" href="logout.php">🚪 Logout</a>
</div>

</body>
</html>