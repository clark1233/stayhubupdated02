<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='customer'){ 
    header("Location: ../login.php"); 
    exit(); 
}

require_once $_SERVER['DOCUMENT_ROOT'].'/stayhub/db.php';

$room_id = $_POST['room_id'] ?? 1;
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$guests = $_POST['guests'] ?? 1;
$payment = $_POST['payment'] ?? 'online';
$user_id = $_SESSION['user_id'];

if(!$checkin || !$checkout || strtotime($checkout) <= strtotime($checkin)){
    die("Invalid check-in or check-out date.");
}

// Room descriptions
$descriptions = [
    "Enjoy a luxurious stay in this modern deluxe room with multiple bed and city view.",
    "Spacious standard room with cozy furniture and serene ambiance.",
    "Suite with private balcony, ocean view, and jacuzzi for ultimate relaxation.",
    "Deluxe room with king-size bed and complimentary breakfast included.",
    "Family room with single bedrooms and extra amenities for children.",
    "Single room perfect for couple travelers with all basic facilities.",
    "Luxury suite with elegant interiors and premium services.",
    "Standard room with workspace and fast Wi-Fi for business travelers.",
    "Deluxe room with modern décor and relaxing seating area.",
    "Suite featuring a private lounge, minibar, and scenic view.",
    "Family room with connecting rooms and spacious living area.",
    "Single cozy room ideal for weekend getaways and short stays.",
    "Deluxe room with large windows and natural sunlight.",
    "Suite with exclusive amenities and complimentary evening snacks.",
    "Standard room offering comfort, simplicity, and convenience."
];

$room_index = max(0,min($room_id-1,14));
$room = [
    'room_name'=>"Room $room_id",
    'room_type'=>($room_id%3==0?'Suite':($room_id%2==0?'Deluxe':'Standard')),
    'price'=>rand(50,250),
    'description'=>$descriptions[$room_index],
    'image'=>"room$room_id.jpg"
];

$checkin_date = new DateTime($checkin);
$checkout_date = new DateTime($checkout);
$nights = $checkin_date->diff($checkout_date)->days;
$total_price = $room['price'] * $nights;
$status = 'Pending'; // booking is initially pending

$stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, room_name, room_type, checkin, checkout, nights, guests, payment_method, total_price, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("iissssiiiss", $user_id,$room_id,$room['room_name'],$room['room_type'],$checkin,$checkout,$nights,$guests,$payment,$total_price,$status);
$stmt->execute();
$booking_id = $stmt->insert_id;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | Booking Pending</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:url('../pic.jpg') no-repeat center center fixed;background-size:cover;font-family:Arial,sans-serif;color:white;min-height:100vh;}
nav{width:100%;background:rgba(0,0,0,0.6);padding:15px 30px;display:flex;justify-content:space-between;align-items:center;position:fixed;top:0;z-index:100;}
nav .logo{font-size:24px;font-weight:bold;color:#ff6b2c;}
nav ul{list-style:none;display:flex;}
nav ul li{margin-left:20px;}
nav ul li a{color:white;text-decoration:none;font-weight:bold;}
nav ul li a:hover{color:#ff6b2c;}
.container{padding-top:90px;width:95%;max-width:800px;margin:0 auto;}
h1{text-align:center;color:#ff6b2c;margin-bottom:30px;text-shadow:2px 2px 5px rgba(0,0,0,0.6);}
.confirm-card{background:rgba(0,0,0,0.7);border-radius:12px;padding:20px;text-align:center;}
.confirm-card img{width:100%;height:250px;object-fit:cover;border-radius:10px;margin-bottom:20px;}
.confirm-card h2{color:#ff6b2c;margin-bottom:10px;text-shadow:1px 1px 4px rgba(0,0,0,0.6);}
.confirm-card p{font-size:16px;margin-bottom:10px;text-shadow:1px 1px 3px rgba(0,0,0,0.6);}
.confirm-card .total{font-weight:bold;font-size:18px;margin-top:15px;margin-bottom:20px;}
.confirm-card a{display:inline-block;padding:10px 25px;font-weight:bold;color:white;background:#ff6b2c;border-radius:6px;text-decoration:none;transition:background 0.3s, box-shadow 0.3s;}
.confirm-card a:hover{background:#e85d22;box-shadow:0 5px 15px rgba(255,107,44,0.5);}
@media(max-width:600px){.confirm-card img{height:200px;}}
</style>
</head>
<body>
<nav>
<div class="logo">StayHub</div>
<ul>
<li><a href="customer_dashboard.php">Dashboard</a></li>
<li><a href="../logout.php">Logout</a></li>
</ul>
</nav>

<div class="container">
<h1>Booking Pending</h1>
<div class="confirm-card">
<img src="<?php echo $room['image']; ?>" alt="Room Image">
<h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
<p><strong>Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
<p><?php echo htmlspecialchars($room['description']); ?></p>
<p><strong>Check-In:</strong> <?php echo $checkin; ?></p>
<p><strong>Check-Out:</strong> <?php echo $checkout; ?> (<?php echo $nights; ?> nights)</p>
<p><strong>Guests:</strong> <?php echo $guests; ?></p>
<p><strong>Payment Method:</strong> <?php echo ucfirst($payment); ?></p>
<p class="total">Total Price: $<?php echo number_format($total_price,2); ?></p>
<p><strong>Status:</strong> Pending</p>
<p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
<!-- FIXED LINK: now works correctly -->
<a href="dashboard.php">Back to Dashboard</a>
</div>
</div>
</body>
</html>
