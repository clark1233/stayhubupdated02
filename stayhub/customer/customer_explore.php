<?php
session_start();

// Only logged-in customers
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

// Unique descriptions for each room
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

// Create 15 room slots with unique descriptions
$rooms = [];
for($i=1;$i<=15;$i++){
    $rooms[] = [
        'room_name' => "Room $i",
        'room_type' => ($i%3==0 ? 'Suite' : ($i%2==0 ? 'Deluxe' : 'Standard')),
        'price' => rand(50,250),
        'description' => $descriptions[$i-1],
        'rating' => rand(4,5),
        'image' => "room{$i}.jpg" // room1.jpg to room15.jpg
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | Explore Rooms</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {
    background: url('../pic.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: Arial,sans-serif;
    color: white;
    min-height: 100vh;
}
nav {
    width:100%;
    background: rgba(0,0,0,0.6);
    padding:15px 30px;
    display:flex;
    justify-content: space-between;
    align-items:center;
    position: fixed;
    top:0;
    z-index:100;
}
nav .logo {font-size:24px; font-weight:bold; color:#ff6b2c;}
nav ul {list-style:none; display:flex;}
nav ul li {margin-left:20px;}
nav ul li a {color:white; text-decoration:none; font-weight:bold;}
nav ul li a:hover {color:#ff6b2c;}

.container {
    padding-top:90px;
    width:95%;
    max-width:1200px;
    margin:0 auto;
}

h1 {
    text-align:center;
    color:#ff6b2c;
    margin-bottom:30px;
    text-shadow:2px 2px 5px rgba(0,0,0,0.6);
}

.rooms {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap:25px;
}

.room-card {
    background: rgba(0,0,0,0.7);
    border-radius:12px;
    overflow: hidden;
    display:flex;
    flex-direction: column;
    transition: transform 0.3s, box-shadow 0.3s;
}
.room-card:hover {
    transform: translateY(-5px);
    box-shadow:0 10px 20px rgba(0,0,0,0.5);
}
.room-card img {
    width:100%;
    height:180px;
    object-fit: cover;
}

.room-content {
    padding:15px;
    flex:1;
    display:flex;
    flex-direction: column;
    justify-content: space-between;
}

.room-content h2 {
    color:#ff6b2c;
    margin-bottom:10px;
    font-size:20px;
    text-shadow:1px 1px 3px rgba(0,0,0,0.6);
}

.room-content p {
    font-size:14px;
    margin-bottom:10px;
    text-shadow:1px 1px 3px rgba(0,0,0,0.6);
}

.room-price {
    font-weight:bold;
    margin-bottom:10px;
}

.room-rating {
    margin-bottom:10px;
    color:#ffd700; /* gold stars */
}

.room-card a {
    display:block;
    padding:10px;
    text-align:center;
    background:#ff6b2c;
    color:white;
    font-weight:bold;
    border-radius:6px;
    text-decoration:none;
    transition: background 0.3s, box-shadow 0.3s;
}
.room-card a:hover {
    background:#e85d22;
    box-shadow:0 5px 15px rgba(255,107,44,0.5);
}

@media(max-width:600px){
    .room-card img {height:150px;}
}
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
    <h1>Explore Rooms</h1>
    <div class="rooms">
        <?php foreach($rooms as $index => $room): ?>
            <div class="room-card">
                <img src="<?php echo $room['image']; ?>" alt="Room Image">
                <div class="room-content">
                    <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
                    <p class="room-price"><strong>Price:</strong> $<?php echo number_format($room['price'],2); ?></p>
                    <p><?php echo htmlspecialchars($room['description']); ?></p>
                    <p class="room-rating">
                        <?php
                        for($i=0;$i<$room['rating'];$i++) echo "★";
                        for($i=$room['rating'];$i<5;$i++) echo "☆";
                        ?>
                    </p>
                    <a href="customer_book.php?room_id=<?php echo $index+1; ?>">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
