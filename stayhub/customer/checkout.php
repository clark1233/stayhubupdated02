<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

if ($_SERVER["REQUEST_METHOD"] != "POST") { header("Location: dashboard.php"); exit(); }

// 1. Get Data from Dashboard
$room = $_POST['room_name'];
$price = $_POST['price'];
$arrival = $_POST['arrival'];
$departure = $_POST['departure'];
$user_id = $_SESSION['user_id'];

// 2. Calculate Total
$nights = (strtotime($departure) - strtotime($arrival)) / 86400;
$total = $price * ($nights <= 0 ? 1 : $nights);

// 3. PRE-SAVE TO DATABASE (Status: Pending)
// We save it now so it's already in the DB before the user goes to PayMongo
$stmt = $conn->prepare("INSERT INTO bookings (user_id, full_name, email, phone, room_name, total_price, arrival, departure, status) VALUES (?, '', '', '', ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("isdss", $user_id, $room, $total, $arrival, $departure);
$stmt->execute();

// Store the ID of this booking to update it later
$booking_id = $conn->insert_id;
$_SESSION['current_booking_id'] = $booking_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | StayHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0b0b0b; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .checkout-container { background: #1a1a1a; width: 500px; padding: 40px; border-radius: 25px; border: 1px solid #333; }
        .step-header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .step { font-size: 12px; color: #555; font-weight: bold; text-transform: uppercase; }
        .step.active { color: #f37021; }
        .form-section { display: none; } .form-section.active { display: block; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border-radius: 10px; border: 1px solid #333; background: #222; color: white; outline: none; }
        .btn { width: 100%; padding: 15px; border-radius: 30px; border: none; font-weight: 800; cursor: pointer; text-transform: uppercase; background: #f37021; color: white; }
        .summary-box { background: #222; padding: 20px; border-radius: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="checkout-container">
    <div class="step-header">
        <span class="step active" id="s1">1. Billing</span>
        <span class="step" id="s2">2. Summary</span>
        <span class="step" id="s3">3. Payment</span>
    </div>

    <form action="payment_process.php" method="POST">
        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <input type="hidden" name="room_name" value="<?php echo $room; ?>">

        <div class="form-section active" id="step1">
            <h3 style="margin-bottom:15px; color:#f37021;">Guest Information</h3>
            <label>FULL NAME</label><input type="text" name="name" required placeholder="Juan Dela Cruz">
            <label>EMAIL</label><input type="email" name="email" required placeholder="juan@example.com">
            <label>PHONE</label><input type="text" name="phone" required placeholder="0917XXXXXXX">
            <button type="button" class="btn" onclick="nextStep(2)">Continue to Summary</button>
        </div>

        <div class="form-section" id="step2">
            <h3 style="margin-bottom:15px; color:#f37021;">Review Stay</h3>
            <div class="summary-box">
                <p>Room: <b><?php echo $room; ?></b></p>
                <p>Nights: <b><?php echo $nights; ?></b></p>
                <p>Check-in: <b><?php echo $arrival; ?></b></p>
            </div>
            <h2 style="margin:20px 0;">Total: ₱<?php echo number_format($total, 2); ?></h2>
            <button type="button" class="btn" onclick="nextStep(3)">Confirm Details</button>
        </div>

        <div class="form-section" id="step3">
            <h3 style="margin-bottom:15px; color:#f37021;">Secure GCash Payment</h3>
            <p style="color: #888; margin-bottom: 20px;">Your booking #<?php echo $booking_id; ?> is saved. Click below to pay.</p>
            <button type="submit" class="btn">Pay with GCash Now</button>
        </div>
    </form>
</div>

<script>
    function nextStep(n) {
        document.querySelectorAll('.form-section, .step').forEach(el => el.classList.remove('active'));
        document.getElementById('step'+n).classList.add('active');
        document.getElementById('s'+n).classList.add('active');
    }
</script>
</body>
</html>