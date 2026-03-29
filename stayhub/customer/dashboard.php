<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

// 1. Security Check
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){ 
    header("Location: ../login.php"); 
    exit(); 
}

// 2. SAFE NAME FETCH
$user_id = $_SESSION['user_id'];
$user_name = "Guest"; // Default fallback

// Try to fetch the name. 
// NOTE: If your column is named 'username', change 'full_name' to 'username' below.
$user_query = $conn->query("SELECT * FROM users WHERE id = '$user_id'");

if ($user_query && $user_query->num_rows > 0) {
    $user_data = $user_query->fetch_assoc();
    
    // This part checks which column exists in your table
    if (isset($user_data['full_name'])) {
        $user_name = $user_data['full_name'];
    } elseif (isset($user_data['username'])) {
        $user_name = $user_data['username'];
    } elseif (isset($user_data['name'])) {
        $user_name = $user_data['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | StayHub Luxury</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; min-height: 100vh; color: white; overflow: hidden; 
        }

        header { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 10px 5%; background: #fff; position: sticky; top: 0; z-index: 1000; 
        }
        .brand-title { font-size: 30px; font-weight: 800; color: #0076a3; text-decoration: none; }
        .brand-title span { color: #f37021; }

        .dashboard-wrapper { display: grid; grid-template-columns: 260px 1fr 400px; height: calc(100vh - 80px); }

        .sidebar { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); padding: 40px 20px; border-right: 1px solid rgba(255,255,255,0.1); }
        .menu-link { text-decoration: none; color: #bbb; font-weight: 600; padding: 15px; display: flex; align-items: center; gap: 15px; border-radius: 12px; margin-bottom: 10px; transition: 0.3s; }
        .menu-link.active { background: #f37021; color: white; }

        .main-content { padding: 40px; overflow-y: auto; }
        .lodging-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        
        .room-card { 
            background: rgba(255,255,255,0.07); padding: 18px; border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.1); transition: 0.3s ease; cursor: pointer;
        }
        .room-card:hover { border-color: #f37021; transform: translateY(-5px); }
        .room-card img { width: 100%; height: 140px; border-radius: 15px; object-fit: cover; }

        .right-panel { background: rgba(0,0,0,0.5); backdrop-filter: blur(30px); padding: 40px 30px; border-left: 1px solid rgba(255,255,255,0.1); }
        .user-profile { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .avatar { width: 50px; height: 50px; background: #e4e6eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #8a8d91; border: 2px solid #0076a3; }

        .book-btn { 
            width: 100%; background: #f37021; color: white; padding: 18px; border-radius: 15px; 
            border: none; font-weight: 800; cursor: pointer; text-transform: uppercase; margin-top: 20px;
        }
        input[type="date"] { width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 5px; background: #fff; color: #333; }
        label { font-size: 12px; color: #f37021; font-weight: 800; display: block; margin-top: 15px; }
    </style>
</head>
<body>

<header>
    <a href="#" class="brand-title">Stay<span>Hub</span></a>
    <nav><a href="../logout.php" style="color:#333; font-weight:800; text-decoration:none;">LOGOUT</a></nav>
</header>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <a href="#" class="menu-link active"><i class="fa-solid fa-hotel"></i> Overview</a>
        <a href="my_bookings.php" class="menu-link"><i class="fa-solid fa-list-check"></i> My Bookings</a>
    </aside>

    <main class="main-content">
        <h2 style="margin-bottom: 25px; border-left: 5px solid #f37021; padding-left: 15px;">Available Rooms</h2>
        <div class="lodging-grid">
            <?php for($i=1; $i<=6; $i++): $price = 1400 + ($i * 200); ?>
            <div class="room-card" onclick="updatePreview('room<?php echo $i; ?>.jpg', 'StayHub Suite <?php echo $i; ?>', '<?php echo $price; ?>')">
                <img src="room<?php echo $i; ?>.jpg">
                <h4 style="color:#f37021; margin-top:10px;">Suite <?php echo $i; ?></h4>
                <p>₱<?php echo number_format($price); ?> / night</p>
            </div>
            <?php endfor; ?>
        </div>
    </main>

    <aside class="right-panel">
        <div class="user-profile">
            <div class="avatar"><i class="fa-solid fa-user"></i></div>
            <div><b><?php echo htmlspecialchars($user_name); ?></b><br><small style="color:#f37021;">Customer</small></div>
        </div>

        <div id="preview-box">
            <img src="room1.jpg" id="display-img" style="width:100%; border-radius:20px; height:180px; object-fit:cover; border:2px solid #f37021;">
            <h4 id="display-name" style="margin-top:15px; font-size:22px;">StayHub Suite 1</h4>
            <p id="display-price" style="font-size:18px; font-weight:800; color:#f37021;">₱1,600 / night</p>
            
            <form action="checkout.php" method="POST">
                <input type="hidden" name="room_name" id="h-name" value="StayHub Suite 1">
                <input type="hidden" name="price" id="h-price" value="1600">
                <label>ARRIVAL DATE</label>
                <input type="date" name="arrival" required>
                <label>DEPARTURE DATE</label>
                <input type="date" name="departure" required>
                <button type="submit" class="book-btn">Proceed to Checkout</button>
            </form>
        </div>
    </aside>
</div>

<script>
function updatePreview(img, name, price) {
    document.getElementById('display-img').src = img;
    document.getElementById('display-name').innerText = name;
    document.getElementById('display-price').innerText = "₱" + parseInt(price).toLocaleString() + " / night";
    document.getElementById('h-name').value = name;
    document.getElementById('h-price').value = price;
}
</script>
</body>
</html>