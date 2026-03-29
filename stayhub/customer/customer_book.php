<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php"); exit();
}

$room_id = $_GET['room_id'] ?? 1;

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

$room_index = max(0, min($room_id - 1, 14));
$room = [
    'room_name'=>"Room $room_id",
    'room_type'=>($room_id%3==0?'Suite':($room_id%2==0?'Deluxe':'Standard')),
    'price'=>rand(50,250),
    'description'=>$descriptions[$room_index],
    'image'=>"room$room_id.jpg"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | Book Room</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:url('../pic.jpg') no-repeat center center fixed; background-size:cover;font-family:Arial,sans-serif;color:white;min-height:100vh;}
nav{width:100%;background:rgba(0,0,0,0.6);padding:15px 30px;display:flex;justify-content:space-between;align-items:center;position:fixed;top:0;z-index:100;}
nav .logo{font-size:24px;font-weight:bold;color:#ff6b2c;}
nav ul{list-style:none;display:flex;}
nav ul li{margin-left:20px;}
nav ul li a{color:white;text-decoration:none;font-weight:bold;}
nav ul li a:hover{color:#ff6b2c;}
.container{padding-top:90px;width:95%;max-width:900px;margin:0 auto;}
h1{text-align:center;color:#ff6b2c;margin-bottom:30px;text-shadow:2px 2px 5px rgba(0,0,0,0.6);}
.booking-card{background:rgba(0,0,0,0.7);border-radius:12px;overflow:hidden;display:flex;flex-direction:column;padding:20px;}
.booking-card img{width:100%;height:250px;object-fit:cover;border-radius:10px;margin-bottom:20px;}
.room-info h2{color:#ff6b2c;font-size:24px;margin-bottom:10px;text-shadow:1px 1px 4px rgba(0,0,0,0.6);}
.room-info p{font-size:16px;margin-bottom:10px;text-shadow:1px 1px 3px rgba(0,0,0,0.6);}
.room-rating{color:#ffd700;margin-bottom:15px;}
form label{display:block;margin-bottom:6px;font-weight:bold;}
form input, form select{width:100%;padding:10px;margin-bottom:15px;border-radius:6px;border:none;outline:none;font-size:14px;}
form button{padding:12px 25px;font-weight:bold;color:white;background:#ff6b2c;border:none;border-radius:6px;cursor:pointer;font-size:16px;transition:background 0.3s, box-shadow 0.3s;}
form button:hover{background:#e85d22;box-shadow:0 5px 15px rgba(255,107,44,0.5);}
@media(max-width:600px){.booking-card img{height:200px;}}
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
    <h1>Book Your Room</h1>

    <div class="booking-card">
        <img src="<?php echo $room['image']; ?>" alt="Room Image">
        <div class="room-info">
            <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
            <p><?php echo htmlspecialchars($room['description']); ?></p>
            <p><strong>Price per Night:</strong> $<?php echo number_format($room['price'],2); ?></p>
        </div>

        <form action="customer_book_confirm.php" method="POST">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

            <label for="checkin">Check-In Date</label>
            <input type="date" name="checkin" id="checkin" required>

            <label for="checkout">Check-Out Date</label>
            <input type="date" name="checkout" id="checkout" required>

            <label for="guests">Number of Guests</label>
            <select name="guests" id="guests" required>
                <option value="1">1 Guest</option>
                <option value="2">2 Guests</option>
                <option value="3">3 Guests</option>
                <option value="4">4 Guests</option>
                <option value="5">5 Guests</option>
            </select>

            <label for="payment">Payment Method</label>
            <select name="payment" id="payment" required>
                <option value="online">Online Payment</option>
                <option value="onsite">Pay on Arrival</option>
            </select>

            <button type="submit">Proceed to Payment & Booking</button>
        </form>
    </div>
</div>
</body>
</html>
