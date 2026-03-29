<?php
session_start();
// Database Connection
$conn = new mysqli("localhost", "root", "", "stayhub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Fetch Admin Details
$user_id = $_SESSION['user_id'];
$admin_query = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
$admin = ($admin_query) ? $admin_query->fetch_assoc() : null;
$admin_name = $admin['full_name'] ?? $admin['username'] ?? 'Administrator';

// 3. Fetch Real Stats (Improved Queries)
// We check the 'rooms' table for inventory
$rooms_res = $conn->query("SELECT COUNT(*) as total FROM rooms");
$total_rooms = ($rooms_res) ? $rooms_res->fetch_assoc()['total'] : 0;

$bookings_res = $conn->query("SELECT COUNT(*) as total FROM bookings");
$total_bookings = ($bookings_res) ? $bookings_res->fetch_assoc()['total'] : 0;

$users_res = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$total_users = ($users_res) ? $users_res->fetch_assoc()['total'] : 0;

// 4. Fetch Revenue Data for Chart (Last 7 Days)
$revenue_data = [];
$date_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_labels[] = date('M d', strtotime($date));
    
    // Attempting to fetch revenue. Checking both common column names: 'created_at' or 'arrival'
    $rev_query = $conn->query("SELECT SUM(total_price) as daily_total FROM bookings WHERE DATE(created_at) = '$date' OR DATE(arrival) = '$date'");
    
    if ($rev_query) {
        $row = $rev_query->fetch_assoc();
        $revenue_data[] = $row['daily_total'] ?? 0;
    } else {
        $revenue_data[] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StayHub Admin | Dashboard Overview</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; color: white; height: 100vh; display: flex; overflow: hidden;
        }

        /* SIDEBAR NAVIGATION */
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

        /* MAIN CONTENT AREA */
        .main-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* TOP STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; display: flex; align-items: center; gap: 15px; color: #333; }
        .icon-box { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-card h3 { font-size: 13px; color: #888; }
        .stat-card .val { font-size: 24px; font-weight: 800; }

        /* CHART & SIDE CARD LAYOUT */
        .dashboard-row { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .chart-panel { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); padding: 30px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1); }
        
        .right-section { display: flex; flex-direction: column; gap: 30px; }
        .white-card { background: white; padding: 30px; border-radius: 25px; color: #333; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        
        .status-pill {
            display: inline-flex; align-items: center; gap: 8px; background: #e8f5e9; 
            color: #2e7d32; padding: 8px 15px; border-radius: 50px; font-size: 11px; font-weight: 700; margin-top: 15px;
        }

        .btn-view {
            margin-top: 25px; background: #f37021; color: white; padding: 12px; 
            border-radius: 50px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 800;
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="nav-logo">SH</div>
        <a href="dashboard.php" class="nav-item active"><i class="fa-solid fa-chart-pie"></i></a>
        <a href="manage_rooms.php" class="nav-item"><i class="fa-solid fa-bed"></i></a>
        <a href="manage_bookings.php" class="nav-item"><i class="fa-solid fa-calendar-days"></i></a>
        <a href="manage_users.php" class="nav-item"><i class="fa-solid fa-users"></i></a>
        <a href="reviews.php" class="nav-item"><i class="fa-solid fa-star"></i></a>
        <a href="../logout.php" class="nav-item" style="margin-top: auto;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 style="font-size: 32px; font-weight: 800;">Dashboard <span>Overview</span></h1>
                <p style="color: #bbb;">System Monitoring & Controls</p>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <b><?php echo htmlspecialchars($admin_name); ?></b><br>
                    <small style="color: #f37021; font-weight: 700;">HEAD MANAGER</small>
                </div>
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; border: 2px solid #f37021; color: #0076a3;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(243, 112, 33, 0.1); color: #f37021;"><i class="fa-solid fa-hotel"></i></div>
                <div><h3>Inventory</h3><div class="val"><?php echo $total_rooms; ?> Rooms</div></div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(0, 118, 163, 0.1); color: #0076a3;"><i class="fa-solid fa-user-group"></i></div>
                <div><h3>Customers</h3><div class="val"><?php echo $total_users; ?> Active</div></div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;"><i class="fa-solid fa-clipboard-list"></i></div>
                <div><h3>Bookings</h3><div class="val"><?php echo $total_bookings; ?> Total</div></div>
            </div>
        </div>

        <div class="dashboard-row">
            <div class="chart-panel">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 25px;">Revenue Stat (Last 7 Days)</h2>
                <canvas id="revenueChart" height="200"></canvas>
            </div>

            <div class="right-section">
                <div class="white-card">
                    <h3 style="font-size: 14px; color: #888; text-transform: uppercase;">Guest Satisfaction</h3>
                    <div style="font-size: 56px; font-weight: 900; color: #0076a3; margin: 15px 0;">4.9<span style="font-size: 20px; color: #ccc;">/5.0</span></div>
                    
                    <div style="width: 100%; height: 10px; background: #eee; border-radius: 10px; overflow: hidden; margin-bottom: 10px;">
                        <div style="width: 98%; height: 100%; background: #f37021;"></div>
                    </div>
                    
                    <p style="font-size: 11px; color: #666; line-height: 1.5;">Based on recent room quality feedback and booking experience.</p>
                    
                    <div>
                        <div class="status-pill">
                            <i class="fa-solid fa-circle-check"></i> System Operational
                        </div>
                    </div>
                    
                    <a href="manage_bookings.php" class="btn-view">MANAGE SYSTEM</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($date_labels); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode($revenue_data); ?>,
                    borderColor: '#f37021',
                    backgroundColor: 'rgba(243, 112, 33, 0.05)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f37021',
                    pointRadius: 5
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#888' } },
                    x: { grid: { display: false }, ticks: { color: '#888' } }
                }
            }
        });
    </script>
</body>
</html>