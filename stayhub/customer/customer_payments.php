<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/stayhub/db.php';

$user_id = $_SESSION['user_id'];

// Handle payment action
if(isset($_GET['pay_booking_id'])){
    $booking_id = intval($_GET['pay_booking_id']);

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=? AND status='Pending'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if($booking){
        $stmt = $conn->prepare("UPDATE bookings SET status='Paid' WHERE id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php?msg=Payment successful for booking ID: $booking_id");
        exit();
    } else {
        $error = "Payment not allowed: Booking either cancelled or already paid.";
    }
}

// Handle delete selected Paid or Cancelled bookings
if(isset($_POST['delete_selected'])){
    if(!empty($_POST['delete_ids'])){
        $ids = $_POST['delete_ids']; // array of booking IDs
        $placeholders = implode(',', array_fill(0,count($ids),'?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id IN ($placeholders) AND user_id=? AND status IN ('Paid','Cancelled')");
        $types .= 'i';
        $ids[] = $user_id;
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?msg=Selected bookings deleted successfully!");
        exit();
    } else {
        $error = "Please select at least one booking to delete.";
    }
}

// Fetch all user bookings
$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id=? ORDER BY id DESC");
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
<title>StayHub | Payments & History</title>
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
    max-width:1000px;
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
a.button, input[type="submit"]{
    display:inline-block;
    padding:8px 15px;
    font-weight:bold;
    color:white;
    background:#ff6b2c;
    border-radius:6px;
    text-decoration:none;
    transition:0.3s;
    cursor:pointer;
}
a.button:hover, input[type="submit"]:hover{
    background:#e85d22;
    box-shadow:0 5px 15px rgba(255,107,44,0.5);
}
input[type="checkbox"]{
    transform:scale(1.2);
    cursor:pointer;
}
.error{color:red;text-align:center;margin-bottom:15px;}
.cancelled{color:#ff6b2c;font-weight:bold;}
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
<h1>Payments & Booking History</h1>

<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

<?php if(count($bookings) == 0): ?>
    <p style="text-align:center;">No bookings found.</p>
<?php else: ?>
<form method="POST">
<table>
<tr>
<th>Select</th>
<th>Booking ID</th>
<th>Room</th>
<th>Check-In</th>
<th>Check-Out</th>
<th>Nights</th>
<th>Guests</th>
<th>Total Price</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php foreach($bookings as $b): ?>
<tr>
<td>
<?php if($b['status'] == 'Paid' || $b['status'] == 'Cancelled'): ?>
    <input type="checkbox" name="delete_ids[]" value="<?php echo $b['id']; ?>">
<?php endif; ?>
</td>
<td><?php echo $b['id']; ?></td>
<td><?php echo htmlspecialchars($b['room_name'] ?? 'Room'); ?></td>
<td><?php echo $b['checkin']; ?></td>
<td><?php echo $b['checkout']; ?></td>
<td><?php echo $b['nights']; ?></td>
<td><?php echo $b['guests']; ?></td>
<td>$<?php echo number_format($b['total_price'],2); ?></td>
<td>
<?php 
if($b['status'] == 'Cancelled'){
    echo "<span class='cancelled'>Cancelled</span>";
} else {
    echo $b['status'];
}
?>
</td>
<td>
<?php 
if($b['status'] == 'Pending'): ?>
    <a class="button" href="?pay_booking_id=<?php echo $b['id']; ?>">Pay Now</a>
<?php elseif($b['status'] == 'Cancelled'): ?>
    <span class='cancelled'>Cancelled</span>
<?php else: ?>
    <span>Paid</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<br>
<input type="submit" name="delete_selected" value="Delete Selected">
</form>
<?php endif; ?>

</div>
</body>
</html>
