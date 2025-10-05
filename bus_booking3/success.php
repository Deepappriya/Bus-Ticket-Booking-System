<!-- success.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Booking Success</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #fcb69f, #ffecd2);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .success-box {
      background-color: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      max-width: 400px;
      width: 90%;
    }

    h3 {
      color: #27ae60;
      font-size: 20px;
      margin-bottom: 30px;
    }

    button {
      background-color: #2980b9;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #1f618d;
    }

    a {
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="success-box">
    <h3>🎉 Booking successful!<br>Your ticket has been generated.</h3>
    <a href="passenger_dashboard.php">
      <button>Go to Dashboard</button>
    </a>
  </div>

</body>
</html>
