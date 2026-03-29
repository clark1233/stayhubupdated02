<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 1. Security: Only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Fetch Admin Details for UI
$admin_id = $_SESSION['user_id'];
$admin_res = $conn->query("SELECT * FROM users WHERE id = '$admin_id'");
$admin_data = $admin_res->fetch_assoc();
$admin_display = $admin_data['username'] ?? $admin_data['email'] ?? 'Admin';

// 3. Delete User Logic
if (isset($_GET['delete'])) {
    $target_id = intval($_GET['delete']);
    // Prevent admin from deleting themselves
    if ($target_id != $admin_id) {
        $conn->query("DELETE FROM users WHERE id = $target_id");
    }
    header("Location: manage_users.php");
    exit();
}

// 4. Update Role Logic (Customer <-> Admin)
if (isset($_GET['promote_id']) && isset($_GET['new_role'])) {
    $uid = intval($_GET['promote_id']);
    $role = mysqli_real_escape_string($conn, $_GET['new_role']);
    $conn->query("UPDATE users SET role = '$role' WHERE id = $uid");
    header("Location: manage_users.php");
    exit();
}

// 5. Fetch all users
$users_query = $conn->query("SELECT * FROM users ORDER BY role ASC, id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StayHub Admin | Manage Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; color: white; height: 100vh; display: flex; overflow: hidden;
        }

        /* SIDEBAR (Matches your theme) */
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

        .main-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        .glass-panel { 
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); 
            padding: 25px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1); 
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #f37021; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1); }
        td { padding: 18px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }

        /* ROLE BADGES */
        .badge { padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .badge-admin { background: #0076a3; color: white; }
        .badge-customer { background: rgba(255,255,255,0.1); color: #bbb; border: 1px solid rgba(255,255,255,0.2); }

        .btn-action { color: #fff; text-decoration: none; font-size: 14px; margin-left: 10px; opacity: 0.6; transition: 0.3s; }
        .btn-action:hover { opacity: 1; color: #f37021; }
        .btn-delete { color: #e74c3c; }
        
        .role-select {
            background: transparent; border: 1px solid rgba(255,255,255,0.2);
            color: white; font-size: 12px; padding: 4px; border-radius: 4px; outline: none;
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="nav-logo">SH</div>
        <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i></a>
        <a href="manage_rooms.php" class="nav-item"><i class="fa-solid fa-bed"></i></a>
        <a href="manage_bookings.php" class="nav-item"><i class="fa-solid fa-calendar-days"></i></a>
        <a href="manage_users.php" class="nav-item active"><i class="fa-solid fa-users"></i></a>
        <a href="reviews.php" class="nav-item"><i class="fa-solid fa-star"></i></a>
        <a href="../logout.php" class="nav-item" style="margin-top: auto;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 style="font-size: 32px; font-weight: 800;">User <span>Management</span></h1>
                <p style="color: #bbb;">Manage accounts and access levels</p>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <b><?php echo htmlspecialchars($admin_display); ?></b><br>
                    <small style="color: #f37021; font-weight: 700;">SYSTEM ADMIN</small>
                </div>
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; border: 2px solid #f37021; color: #0076a3;">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
            </div>
        </div>

        <div class="glass-panel">
            <table>
                <thead>
                    <tr>
                        <th>User Identity</th>
                        <th>Role</th>
                        <th>Registered Date</th>
                        <th>Access Control</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_query->num_rows > 0): ?>
                        <?php while($user = $users_query->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <b><?php echo htmlspecialchars($user['username'] ?? 'No Name'); ?></b><br>
                                <small style="color: #888;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></small>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $user['role']; ?>">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td><small><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></small></td>
                            <td>
                                <?php if($user['id'] != $admin_id): ?>
                                <select class="role-select" onchange="location.href='manage_users.php?promote_id=<?php echo $user['id']; ?>&new_role='+this.value">
                                    <option value="customer" <?php if($user['role'] == 'customer') echo 'selected'; ?>>Customer</option>
                                    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                </select>
                                <?php else: ?>
                                <small style="color: #2ecc71;">Current Session</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user['id'] != $admin_id): ?>
                                <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this user permanently?')">
                                    <i class="fa-solid fa-user-xmark"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 40px; color: #888;">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>