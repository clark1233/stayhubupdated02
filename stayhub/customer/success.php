<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

// 1. Get the booking ID from the URL or Session
$booking_id = $_GET['id'] ?? $_SESSION['current_booking_id'] ?? null;

if ($booking_id) {
    // 2. Automatically update status to Paid
    $conn->query("UPDATE bookings SET status = 'Paid' WHERE id = '$booking_id'");
    
    // 3. Clear the session so the process doesn't repeat on refresh
    unset($_SESSION['current_booking_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful | StayHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; height: 100vh; color: white; 
            display: flex; justify-content: center; align-items: center;
        }

        .glass-card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(25px); 
            padding: 60px 40px; 
            border-radius: 30px; 
            border: 1px solid rgba(255,255,255,0.1); 
            text-align: center; 
            width: 450px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: scaleUp 0.5s ease-out;
        }

        @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .notif-banner { 
            background: rgba(46, 204, 113, 0.2); 
            border: 1px solid #2ecc71; 
            color: #2ecc71;
            padding: 15px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            font-weight: 800;
            font-size: 14px;
            display: inline-block;
            width: 100%;
        }

        .icon-box { font-size: 70px; color: #f37021; margin-bottom: 20px; }
        
        h1 { font-size: 32px; margin-bottom: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        p { color: #bbb; margin-bottom: 35px; line-height: 1.6; }

        .btn-dashboard { 
            background: #f37021; 
            color: white; 
            padding: 16px 40px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: 800; 
            display: inline-block;
            text-transform: uppercase; 
            transition: 0.3s; 
            font-size: 14px;
            letter-spacing: 1px;
            border: none;
        }
        .btn-dashboard:hover { 
            background: #e67e22; 
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(243, 112, 33, 0.3);
        }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="notif-banner">
        <i class="fa-solid fa-circle-check"></i> GCash Payment Successfully Received
    </div>

    <div class="icon-box">
        <i class="fa-solid fa-heart"></i>
    </div>

    <h1>Thank You!</h1>
    <p>Your transaction for Booking #<?php echo htmlspecialchars($booking_id); ?> is complete. Your room is now officially reserved. We look forward to your stay!</p>
    
    <a href="dashboard.php" class="btn-dashboard">
        <i class="fa-solid fa-house" style="margin-right: 8px;"></i> Return to Dashboard
    </a>
</div>

</body>
</html>