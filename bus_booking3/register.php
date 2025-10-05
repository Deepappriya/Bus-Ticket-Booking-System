<?php
include 'db.php';

$message = '';
$type = ''; // Will be 'success' or 'error'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "⚠️ Email already registered.";
        $type = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            $message = "✅ Registered successfully!";
            $type = 'success';
        } else {
            $message = "❌ Error: " . $stmt->error;
            $type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Registration Status</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .message-box {
      background: white;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      text-align: center;
      max-width: 400px;
    }

    .message-box h2 {
      color: <?= ($type == 'success') ? '#2ecc71' : '#e74c3c' ?>;
      font-size: 20px;
      margin-bottom: 20px;
    }

    .message-box a {
      text-decoration: none;
      display: inline-block;
      margin-top: 15px;
      background-color: #2980b9;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .message-box a:hover {
      background-color: #1f618d;
    }
  </style>
</head>
<body>

<div class="message-box">
  <h2><?= $message ?></h2>
  <a href="login.html">🔐 Go to Login</a>
</div>

</body>
</html>
