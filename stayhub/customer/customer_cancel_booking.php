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

// Check if booking exists and belongs to the user
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if(!$booking){
    die("Booking not found or you don't have permission to cancel it.");
}

if($booking['status'] != 'Pending'){
    die("Only pending bookings can be canceled.");
}

// Update booking status to Canceled
$stmt = $conn->prepare("UPDATE bookings SET status='Canceled' WHERE id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->close();

// Redirect back to bookings page
header("Location: customer_view_bookings.php");
exit();
