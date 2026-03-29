<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

// 1. Security Check
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Guest';

// 2. Fetch Bookings (Sorted by newest first)
$query = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | StayHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; min-height: 100vh; color: white; overflow: hidden;
        }

        /* Consistent Header */
        header { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 10px 5%; background: #fff; position: sticky; top: 0; z-index: 1000; 
        }
        .brand-title { font-size: 30px; font-weight: 800; color: #0076a3; text-decoration: none; }
        .brand-title span { color: #f37021; }

        /* Dashboard Layout */
        .dashboard-wrapper { display: grid; grid-template-columns: 260px 1fr; height: calc(100vh - 80px); }

        /* Sidebar Glassmorphism */
        .sidebar { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(20px); 
            padding: 40px 20px; 
            border-right: 1px solid rgba(255,255,255,0.1); 
        }
        .menu-link { 
            text-decoration: none; color: #bbb; font-weight: 600; padding: 15px; 
            display: flex; align-items: center; gap: 15px; border-radius: 12px; 
            margin-bottom: 10px; transition: 0.3s; 
        }
        .menu-link:hover { color: white; background: rgba(255,255,255,0.1); }
        .menu-link.active { background: #f37021; color: white; }

        /* Main Content Area */
        .main-content { padding: 40px; overflow-y: auto; }
        
        /* Table Styling */
        .booking-container {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        table { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
        th { 
            text-align: left; padding: 10px 20px; color: #f37021; 
            text-transform: uppercase; font-size: 11px; letter-spacing: 1px; 
        }
        td { 
            padding: 20px; background: rgba(255,255,255,0.05); 
            font-size: 14px; border-top: 1px solid rgba(255,255,255,0.05);
        }
        td:first-child { border-radius: 15px 0 0 15px; }
        td:last-child { border-radius: 0 15px 15px 0; }

        /* Status Badges */
        .badge {
            padding: 6px 16px; border-radius: 50px; font-size: 11px; 
            font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px;
        }
        .paid { 
            background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid #2ecc71; 
        }
        .pending { 
            background: rgba(241, 196, 15, 0.15); color: #f1c40f; border: 1px solid #f1c40f; 
        }

        .price-text { color: #f37021; font-weight: 800; font-size: 16px; }
        .room-info b { font-size: 16px; color: #fff; display: block; }
        .room-info span { font-size: 12px; color: #888; }
    </style>
</head>
<body>

<header>
    <a href="dashboard.php" class="brand-title">Stay<span>Hub</span></a>
    <nav><a href="../logout.php" style="color:#333; font-weight:800; text-decoration:none;">LOGOUT</a></nav>
</header>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <a href="dashboard.php" class="menu-link"><i class="fa-solid fa-hotel"></i> Overview</a>
        <a href="my_bookings.php" class="menu-link active"><i class="fa-solid fa-list-check"></i> My Bookings</a>
        <a href="#" class="menu-link"><i class="fa-solid fa-user"></i> Profile</a>
    </aside>

    <main class="main-content">
        <h2 style="margin-bottom: 30px; border-left: 5px solid #f37021; padding-left: 20px; text-transform: uppercase; letter-spacing: 1px;">
            My Reservations
        </h2>
        
        <div class="booking-container">
            <?php if($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Room Details</th>
                            <th>Stay Duration</th>
                            <th>Total Price</th>
                            <th style="text-align: center;">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="room-info">
                                    <b><?php echo htmlspecialchars($row['room_name']); ?></b>
                                    <span>Ref ID: #SH-<?php echo $row['id']; ?></span>
                                </td>
                                <td>
                                    <i class="fa-regular fa-calendar" style="color: #f37021; margin-right: 8px;"></i>
                                    <?php echo date('M d', strtotime($row['arrival'])); ?> - 
                                    <?php echo date('M d, Y', strtotime($row['departure'])); ?>
                                </td>
                                <td class="price-text">₱<?php echo number_format($row['total_price'], 2); ?></td>
                                <td style="text-align: center;">
                                    <?php if($row['status'] == 'Paid'): ?>
                                        <div class="badge paid"><i class="fa-solid fa-check-circle"></i> Paid</div>
                                    <?php else: ?>
                                        <div class="badge pending"><i class="fa-solid fa-clock"></i> Pending</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 0;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 50px; color: rgba(255,255,255,0.1); margin-bottom: 20px; display: block;"></i>
                    <p style="color: #666; font-size: 18px;">You don't have any bookings yet.</p>
                    <a href="dashboard.php" style="color: #f37021; text-decoration: none; font-weight: 800; margin-top: 15px; display: inline-block;">Book your first room &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>