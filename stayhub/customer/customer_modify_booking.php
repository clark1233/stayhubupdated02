<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/stayhub/db.php';

$user_id = $_SESSION['user_id'];
$booking_id = $_GET['booking_id'] ?? 0;

if(!$booking_id){
    die("Invalid booking ID.");
}

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if(!$booking){
    die("Booking not found or you don't have permission to modify it.");
}

if($booking['status'] != 'Pending'){
    die("Only pending bookings can be modified.");
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = intval($_POST['guests']);

    if(!$checkin || !$checkout || strtotime($checkout) <= strtotime($checkin)){
        $error = "Invalid check-in or check-out date.";
    } else {
        $nights = (new DateTime($checkin))->diff(new DateTime($checkout))->days;
        $price_per_night = $booking['total_price'] / $booking['nights'];
        $total_price = $price_per_night * $nights;

        // ✅ Fixed bind_param types
        $stmt = $conn->prepare("UPDATE bookings SET checkin=?, checkout=?, nights=?, guests=?, total_price=? WHERE id=?");
        $stmt->bind_param("ssiidi", $checkin, $checkout, $nights, $guests, $total_price, $booking_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to dashboard with notification
        header("Location: dashboard.php?msg=Booking updated successfully!");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | Modify Booking</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    background:url('../pic.jpg') no-repeat center center fixed;
    background-size:cover;
    font-family:Arial,sans-serif;
    color:white;
    min-height:100vh;
}
nav{
    width:100%;
    background:rgba(0,0,0,0.6);
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:fixed;
    top:0;
    z-index:100;
}
nav .logo{font-size:24px;font-weight:bold;color:#ff6b2c;}
nav ul{list-style:none;display:flex;}
nav ul li{margin-left:20px;}
nav ul li a{color:white;text-decoration:none;font-weight:bold;}
nav ul li a:hover{color:#ff6b2c;}
.container{
    padding-top:90px;
    width:95%;
    max-width:600px;
    margin:0 auto;
}
h1{text-align:center;color:#ff6b2c;margin-bottom:30px;text-shadow:2px 2px 5px rgba(0,0,0,0.6);}
form{
    background:rgba(0,0,0,0.7);
    padding:20px;
    border-radius:12px;
}
form label{display:block;margin-bottom:8px;font-weight:bold;}
form input[type="date"], form input[type="number"]{
    width:100%;
    padding:8px;
    margin-bottom:15px;
    border-radius:6px;
    border:none;
}
form input[type="submit"]{
    background:#ff6b2c;
    color:white;
    padding:10px 20px;
    border:none;
    border-radius:6px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}
form input[type="submit"]:hover{
    background:#e85d22;
    box-shadow:0 5px 15px rgba(255,107,44,0.5);
}
.error{color:red;margin-bottom:15px;}
</style>
</head>
<body>
<nav>
<div class="logo">StayHub</div>
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="../logout.php">Logout</a></li>
</ul>
</nav>

<div class="container">
<h1>Modify Booking</h1>

<form method="POST">
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <label>Check-In Date:</label>
    <input type="date" name="checkin" value="<?php echo htmlspecialchars($booking['checkin']); ?>" required>

    <label>Check-Out Date:</label>
    <input type="date" name="checkout" value="<?php echo htmlspecialchars($booking['checkout']); ?>" required>

    <label>Number of Guests:</label>
    <input type="number" name="guests" min="1" value="<?php echo htmlspecialchars($booking['guests']); ?>" required>

    <input type="submit" value="Update Booking">
</form>
</div>
</body>
</html>
