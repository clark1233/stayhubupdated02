<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/stayhub/db.php';

$user_id = $_SESSION['user_id'];

// Handle new review submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])){
    $booking_id = intval($_POST['booking_id']);
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review']);

    if($rating < 1 || $rating > 5 || empty($review_text)){
        $error = "Please provide a valid rating (1-5) and review text.";
    } else {
        // Check if booking belongs to user and is Paid/Completed
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=? AND status IN ('Paid','Completed')");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $booking = $res->fetch_assoc();
        $stmt->close();

        if($booking){
            // Insert review
            $stmt = $conn->prepare("INSERT INTO reviews (booking_id, user_id, room_name, rating, review_text) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iissi", $booking_id, $user_id, $booking['room_name'], $rating, $review_text);
            $stmt->execute();
            $stmt->close();

            // ✅ Redirect back to dashboard after submission
            header("Location: dashboard.php?msg=Review submitted successfully!");
            exit();
        } else {
            $error = "Cannot review this booking.";
        }
    }
}

// Fetch bookings eligible for review
$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id=? AND status IN ('Paid','Completed') ORDER BY checkout DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>StayHub | Reviews & Ratings</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    background:url('../pic.jpg') no-repeat center center fixed;
    background-size:cover;
    font-family:Arial,sans-serif;
    color:white;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
nav{
    width:100%;
    background:rgba(0,0,0,0.7);
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:fixed;
    top:0;
    z-index:100;
}
nav .logo{font-size:26px;font-weight:bold;color:#ff6b2c;}
nav ul{list-style:none;display:flex;}
nav ul li{margin-left:20px;}
nav ul li a{color:white;text-decoration:none;font-weight:bold;transition:0.3s;}
nav ul li a:hover{color:#ff6b2c;}
.container{
    padding-top:90px;
    width:95%;
    max-width:900px;
    margin:0 auto;
}
h1{text-align:center;color:#ff6b2c;margin-bottom:20px;text-shadow:2px 2px 5px rgba(0,0,0,0.6);}
table{
    width:100%;
    border-collapse:collapse;
    background: rgba(0,0,0,0.6);
    border-radius:12px;
    overflow:hidden;
}
table th, table td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.2);
}
th{color:#ff6b2c;}
a.button{
    display:inline-block;
    padding:8px 15px;
    font-weight:bold;
    color:white;
    background:#ff6b2c;
    border-radius:6px;
    text-decoration:none;
    transition:0.3s;
}
a.button:hover{
    background:#e85d22;
    box-shadow:0 5px 15px rgba(255,107,44,0.5);
}
form textarea{
    width:90%;
    padding:8px;
    margin:5px 0;
    border-radius:6px;
    border:none;
    resize:vertical;
}
form select{
    padding:6px;
    border-radius:6px;
    margin-bottom:5px;
}
input[type="submit"]{
    background:#ff6b2c;
    color:white;
    padding:8px 15px;
    border:none;
    border-radius:6px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}
input[type="submit"]:hover{
    background:#e85d22;
    box-shadow:0 5px 15px rgba(255,107,44,0.5);
}
.error{color:red;text-align:center;margin-bottom:15px;}
.msg{
    background:rgba(0,0,0,0.7);
    color:#ff6b2c;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
    text-align:center;
    max-width:600px;
    font-weight:bold;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    margin-left:auto;
    margin-right:auto;
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
<h1>My Reviews & Ratings</h1>

<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

<?php if(count($bookings) == 0): ?>
    <p style="text-align:center;">No bookings available for review.</p>
<?php else: ?>
<table>
<tr>
<th>Booking ID</th>
<th>Room</th>
<th>Check-Out</th>
<th>Review</th>
</tr>
<?php foreach($bookings as $b): ?>
<tr>
<td><?php echo $b['id']; ?></td>
<td><?php echo htmlspecialchars($b['room_name']); ?></td>
<td><?php echo $b['checkout']; ?></td>
<td>
<form method="POST">
<input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
<select name="rating" required>
    <option value="">Rating</option>
    <option value="1">1 ⭐</option>
    <option value="2">2 ⭐⭐</option>
    <option value="3">3 ⭐⭐⭐</option>
    <option value="4">4 ⭐⭐⭐⭐</option>
    <option value="5">5 ⭐⭐⭐⭐⭐</option>
</select>
<br>
<textarea name="review" rows="2" placeholder="Write your review..." required></textarea>
<br>
<input type="submit" value="Submit">
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

</div>
</body>
</html>
