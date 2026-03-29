<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/stayhub/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user bookings
$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | My Bookings</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:url('../pic.jpg') no-repeat center center fixed;background-size:cover;font-family:Arial,sans-serif;color:white;min-height:100vh;}
nav{width:100%;background:rgba(0,0,0,0.6);padding:15px 30px;display:flex;justify-content:space-between;align-items:center;position:fixed;top:0;z-index:100;}
nav .logo{font-size:24px;font-weight:bold;color:#ff6b2c;}
nav ul{list-style:none;display:flex;}
nav ul li{margin-left:20px;}
nav ul li a{color:white;text-decoration:none;font-weight:bold;}
nav ul li a:hover{color:#ff6b2c;}
.container{padding-top:90px;width:95%;max-width:1000px;margin:0 auto;}
h1{text-align:center;color:#ff6b2c;margin-bottom:30px;text-shadow:2px 2px 5px rgba(0,0,0,0.6);}
.booking-card{background:rgba(0,0,0,0.7);border-radius:12px;padding:20px;margin-bottom:20px;}
.booking-card h2{color:#ff6b2c;margin-bottom:10px;text-shadow:1px 1px 4px rgba(0,0,0,0.6);}
.booking-card p{font-size:16px;margin-bottom:8px;text-shadow:1px 1px 3px rgba(0,0,0,0.6);}
.booking-card .status{font-weight:bold;margin-bottom:10px;}
.booking-card a.button{display:inline-block;margin-right:10px;padding:8px 18px;font-weight:bold;color:white;background:#ff6b2c;border-radius:6px;text-decoration:none;transition:background 0.3s, box-shadow 0.3s;}
.booking-card a.button:hover{background:#e85d22;box-shadow:0 5px 15px rgba(255,107,44,0.5);}
@media(max-width:600px){.booking-card{padding:15px;}}
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
<h1>My Bookings</h1>

<?php if(empty($bookings)): ?>
    <p style="text-align:center;font-size:18px;">You have no bookings yet.</p>
<?php else: ?>
    <?php foreach($bookings as $b): ?>
        <div class="booking-card">
            <h2><?php echo htmlspecialchars($b['room_name']); ?> (<?php echo htmlspecialchars($b['room_type']); ?>)</h2>
            <p><strong>Check-In:</strong> <?php echo $b['checkin']; ?></p>
            <p><strong>Check-Out:</strong> <?php echo $b['checkout']; ?> (<?php echo $b['nights']; ?> nights)</p>
            <p><strong>Guests:</strong> <?php echo $b['guests']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo ucfirst($b['payment_method']); ?></p>
            <p><strong>Total Price:</strong> $<?php echo number_format($b['total_price'],2); ?></p>
            <p class="status"><strong>Status:</strong> <?php echo $b['status']; ?></p>
            
            <?php if($b['status'] == 'Pending'): ?>
                <!-- Only pending bookings can be modified or canceled -->
                <a href="customer_modify_booking.php?booking_id=<?php echo $b['id']; ?>" class="button">Modify</a>
                <a href="customer_cancel_booking.php?booking_id=<?php echo $b['id']; ?>" class="button" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>
</body>
</html>
