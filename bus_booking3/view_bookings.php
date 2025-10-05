<?php
session_start();
include 'db.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM bookings WHERE id = $delete_id");
    header("Location: view_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(to right,#43cea2, #185a9d);
      color: #333;
    }

    h2 {
      text-align: center;
      color: #fff;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
    }

    th {
      background-color: #ff9a9e;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .btn-delete {
      background-color: #e74c3c;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-delete:hover {
      background-color: #c0392b;
    }

    .top-bar {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 15px;
}

.btn-back {
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
}

.btn-back:hover {
  background-color: #27ae60;
}

    @media (max-width: 768px) {
      table {
        font-size: 14px;
      }

      th, td {
        padding: 8px;
      }
    }
  </style>
</head>

<body>
<div class="top-bar">
  <a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
</div>
<div style="text-align: center; margin-bottom: 20px;">
  <form method="GET" action="view_bookings.php">
    <input type="text" name="search_bus" placeholder="Search by Bus Name" style="padding: 8px; width: 200px;">
    <!--<input type="text" name="search_bus" placeholder="Bus Name" value="<?= htmlspecialchars($_GET['search_bus'] ?? '') ?>" style="padding: 8px; width: 150px;">-->
    <input type="text" name="search_source" placeholder="Source" value="<?= htmlspecialchars($_GET['search_source'] ?? '') ?>" style="padding: 8px; width: 150px;">
    <input type="text" name="search_destination" placeholder="Destination" value="<?= htmlspecialchars($_GET['search_destination'] ?? '') ?>" style="padding: 8px; width: 150px;">
    <!--<input type="date" name="search_date" value="<?= htmlspecialchars($_GET['search_date'] ?? '') ?>" style="padding: 8px; width: 150px;">-->
    
    <button type="submit" class="btn-back">🔍 Search</button>
  </form>
</div>
<h2>📖<?php
    if (!empty($search_bus) || !empty($search_source) || !empty($search_destination)) {
      echo "Search Results";
    } else {
      echo "All Bookings";
    }
  ?></h2>

<table>
    <tr>
        <th>#</th>
        <th>Passenger Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Bus</th>
        <th>Route</th>
        <th>Travel Date</th>
        <th>Fare</th>
        <th>Action</th>
    </tr>

    <?php
$search_bus = $conn->real_escape_string($_GET['search_bus'] ?? '');
$search_source = $conn->real_escape_string($_GET['search_source'] ?? '');
$search_destination = $conn->real_escape_string($_GET['search_destination'] ?? '');
//$search_date = $conn->real_escape_string($_GET['search_date'] ?? '');

$sql = "SELECT b.id, b.passenger_name, b.email, b.phone, b.travel_date, b.fare,
               bi.bus_name, r.source, r.destination
        FROM bookings b
        JOIN bus_info bi ON b.bus_id = bi.id
        JOIN routes r ON b.route_id = r.id
        WHERE 1=1";

if (!empty($search_bus)) {
    $sql .= " AND bi.bus_name LIKE '%$search_bus%'";
}
if (!empty($search_source)) {
    $sql .= " AND r.source LIKE '%$search_source%'";
}
if (!empty($search_destination)) {
    $sql .= " AND r.destination LIKE '%$search_destination%'";
}
//if (!empty($search_date)) {
//    $sql .= " AND b.travel_date = '$search_date'";
//}

$sql .= " ORDER BY b.id DESC";


    $result = $conn->query($sql);
    $serial = 1;

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $serial++ . "</td>
                <td>{$row['passenger_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['bus_name']}</td>
                <td>{$row['source']} → {$row['destination']}</td>
                <td>{$row['travel_date']}</td>
                <td>₹{$row['fare']}</td>
                <td><a class='delete-btn' href='?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this booking?');\">Delete</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No bookings found.</td></tr>";
    }
    ?>
</table>
</body>
</html>
