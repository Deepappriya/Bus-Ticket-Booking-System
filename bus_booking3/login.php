<?php
session_start();
include 'db.php';

$message = '';
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $email;

        if ($user['role'] == 'passenger') {
            header("Location: passenger_dashboard.php");
            exit();
        } else {
            header("Location: admin_dashboard.php");
            exit();
        }
    } else {
        $message = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Status</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #ff9a9e, #fad0c4);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-box {
      background: white;
      padding: 35px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      text-align: center;
      max-width: 400px;
    }

    h2 {
      color: #e74c3c;
      font-size: 22px;
      margin-bottom: 20px;
    }

    a {
      text-decoration: none;
      display: inline-block;
      background-color: #2980b9;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    a:hover {
      background-color: #1f618d;
    }
  </style>
</head>
<body>

<?php if ($message): ?>
  <div class="login-box">
    <h2><?= $message ?></h2>
    <a href="login.html">🔐 Try Again</a>
  </div>
<?php endif; ?>

</body>
</html>
