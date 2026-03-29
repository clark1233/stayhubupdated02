<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "stayhub");

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// 2. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 3. Fetch Admin Details for Top Bar
$admin_id = $_SESSION['user_id'];
$admin_res = $conn->query("SELECT * FROM users WHERE id = '$admin_id'");
$admin_data = $admin_res->fetch_assoc();
$admin_display_name = $admin_data['username'] ?? $admin_data['email'] ?? 'Admin';

// 4. Delete Booking Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE id = $id");
    header("Location: manage_bookings.php");
    exit();
}

// 5. Update Status Logic
if (isset($_GET['update_id']) && isset($_GET['new_status'])) {
    $bid = intval($_GET['update_id']);
    $status = mysqli_real_escape_string($conn, $_GET['new_status']);
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $bid");
    header("Location: manage_bookings.php");
    exit();
}

// 6. Simplified Fetch (Pulling directly from bookings table)
$bookings_query = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StayHub Admin | Manage Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; color: white; height: 100vh; display: flex; overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 100px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column;
            align-items: center; padding: 30px 0;
        }
        .nav-logo {
            width: 50px; height: 50px; background: #0076a3; color: white; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-weight: 800; margin-bottom: 50px;
        }
        .nav-item { color: #888; font-size: 20px; margin-bottom: 35px; text-decoration: none; transition: 0.3s; }
        .nav-item:hover, .nav-item.active { color: #f37021; }

        /* CONTENT AREA */
        .main-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .glass-panel { 
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); 
            padding: 25px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1); 
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #f37021; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }

        .status-pill {
            padding: 4px 10px; border-radius: 5px; font-size: 10px; font-weight: 800; text-transform: uppercase;
        }
        .status-Paid, .status-Confirmed { background: #2ecc71; color: #fff; }
        .status-Pending { background: #f39c12; color: #fff; }
        .status-Cancelled { background: #e74c3c; color: #fff; }

        .btn-delete {
            color: #e74c3c; background: rgba(231, 76, 60, 0.1); padding: 8px 12px; 
            border-radius: 8px; text-decoration: none; transition: 0.3s;
        }
        .btn-delete:hover { background: #e74c3c; color: #fff; }

        .status-select {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            color: white; padding: 5px; border-radius: 5px; outline: none; font-size: 12px;
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="nav-logo">SH</div>
        <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i></a>
        <a href="manage_rooms.php" class="nav-item"><i class="fa-solid fa-bed"></i></a>
        <a href="manage_bookings.php" class="nav-item active"><i class="fa-solid fa-calendar-days"></i></a>
        <a href="manage_users.php" class="nav-item"><i class="fa-solid fa-users"></i></a>
        <a href="reviews.php" class="nav-item"><i class="fa-solid fa-star"></i></a>
        <a href="../logout.php" class="nav-item" style="margin-top: auto;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 style="font-size: 32px; font-weight: 800;">Booking <span>Records</span></h1>
                <p style="color: #bbb;">Direct database overview of all stays</p>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <b><?php echo htmlspecialchars($admin_display_name); ?></b><br>
                    <small style="color: #f37021; font-weight: 700;">HEAD MANAGER</small>
                </div>
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; border: 2px solid #f37021; color: #0076a3;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>

        <div class="glass-panel">
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings_query && $bookings_query->num_rows > 0): ?>
                        <?php while($row = $bookings_query->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <b><?php echo htmlspecialchars($row['full_name'] ?? 'Guest'); ?></b><br>
                                <small style="color: #888;"><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                            </td>
                            <td><span style="color: #0076a3; font-weight: 700;"><?php echo htmlspecialchars($row['room_name']); ?></span></td>
                            <td>
                                <small>In: <?php echo date('M d', strtotime($row['arrival'])); ?></small><br>
                                <small>Out: <?php echo date('M d', strtotime($row['departure'])); ?></small>
                            </td>
                            <td style="color: #2ecc71; font-weight: 800;">₱<?php echo number_format($row['total_price']); ?></td>
                            <td>
                                <select class="status-select" onchange="location.href='manage_bookings.php?update_id=<?php echo $row['id']; ?>&new_status='+this.value">
                                    <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Confirmed" <?php if($row['status'] == 'Confirmed' || $row['status'] == 'Paid') echo 'selected'; ?>>Confirmed</option>
                                    <option value="Cancelled" <?php if($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <a href="manage_bookings.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Permanently delete this booking?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #888;">No booking records found in database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>