<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $price = $conn->real_escape_string($_POST['price']);

    $sql = "INSERT INTO bookings (user_id, room_name, price) VALUES ('$user_id', '$room_name', '$price')";
    if ($conn->query($sql) === TRUE) { $success = true; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #111; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
        .card { background: #222; padding: 50px; border-radius: 20px; border: 1px solid #f37021; }
        .icon { font-size: 50px; color: #f37021; margin-bottom: 20px; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #f37021; color: white; text-decoration: none; border-radius: 30px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✔</div>
        <h2>Booking Successful!</h2>
        <p>Your reservation for <?php echo $room_name; ?> is confirmed.</p>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>