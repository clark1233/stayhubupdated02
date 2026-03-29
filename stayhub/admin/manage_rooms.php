<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

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

// 3. Toggle Status Logic
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $current = $_GET['current'];
    $new_status = ($current == 'Available') ? 'Not Available' : 'Available';
    $conn->query("UPDATE rooms SET status = '$new_status' WHERE id = $id");
    header("Location: manage_rooms.php");
}

// 4. Save/Update Logic
if (isset($_POST['save_room'])) {
    $name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $price = $_POST['price'];
    $status = $_POST['status'];
    $rating = $_POST['rating'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    if (!empty($_FILES['room_image']['name'])) {
        $img_name = time() . "_" . $_FILES['room_image']['name'];
        move_uploaded_file($_FILES['room_image']['tmp_name'], "../rooms/" . $img_name);
    } else {
        $img_name = $_POST['existing_image'] ?: $_POST['image_filename_manual'];
    }

    if (!empty($_POST['room_id'])) {
        $id = $_POST['room_id'];
        $conn->query("UPDATE rooms SET room_name='$name', price='$price', status='$status', image='$img_name', description='$desc', rating='$rating' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO rooms (room_name, price, status, image, description, rating) VALUES ('$name', '$price', '$status', '$img_name', '$desc', '$rating')");
    }
    header("Location: manage_rooms.php");
}

// 5. Delete Logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id = $id");
    header("Location: manage_rooms.php");
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM rooms WHERE id = $id");
    if($res) $edit_data = $res->fetch_assoc();
}

$rooms_query = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StayHub Admin | Manage Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('../pic.jpg') no-repeat center center fixed;
            background-size: cover; color: white; height: 100vh; display: flex; overflow: hidden;
        }

        /* SIDEBAR NAVIGATION (Consistent with Dashboard) */
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

        /* GLASS PANELS */
        .glass-panel { 
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); 
            padding: 25px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1); 
            margin-bottom: 30px;
        }

        /* FORM STYLING */
        .form-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .full-width { grid-column: span 4; }
        .form-group label { display: block; font-size: 11px; color: #f37021; margin-bottom: 5px; text-transform: uppercase; font-weight: 700; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; background: rgba(255,255,255,0.1); 
            border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; outline: none; 
        }

        .btn-action {
            grid-column: span 4; background: #f37021; color: white; border: none; padding: 12px; 
            border-radius: 50px; font-weight: 800; cursor: pointer; text-transform: uppercase;
        }

        /* TABLE STYLING */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #f37021; font-size: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        
        .room-img { width: 60px; height: 45px; border-radius: 8px; object-fit: cover; }
        
        .status-pill {
            padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 800;
            text-decoration: none; display: inline-block;
        }
        .status-Available { background: #e8f5e9; color: #2e7d32; }
        .status-Not-Available { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="nav-logo">SH</div>
        <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i></a>
        <a href="manage_rooms.php" class="nav-item active"><i class="fa-solid fa-bed"></i></a>
        <a href="manage_bookings.php" class="nav-item"><i class="fa-solid fa-calendar-days"></i></a>
        <a href="manage_users.php" class="nav-item"><i class="fa-solid fa-users"></i></a>
        <a href="reviews.php" class="nav-item"><i class="fa-solid fa-star"></i></a>
        <a href="../logout.php" class="nav-item" style="margin-top: auto;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 style="font-size: 32px; font-weight: 800;">Room <span>Inventory</span></h1>
                <p style="color: #bbb;">Manage Listings & Availability</p>
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

        <div class="glass-panel">
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="room_id" value="<?php echo $edit_data['id'] ?? ''; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $edit_data['image'] ?? ''; ?>">

                <div class="form-group"><label>Room Name</label>
                    <input type="text" name="room_name" value="<?php echo $edit_data['room_name'] ?? ''; ?>" required>
                </div>
                <div class="form-group"><label>Price (₱)</label>
                    <input type="number" name="price" value="<?php echo $edit_data['price'] ?? ''; ?>" required>
                </div>
                <div class="form-group"><label>Rating</label>
                    <input type="number" step="0.1" name="rating" value="<?php echo $edit_data['rating'] ?? '5.0'; ?>">
                </div>
                <div class="form-group"><label>Initial Status</label>
                    <select name="status">
                        <option value="Available" <?php if(($edit_data['status']??'')=='Available') echo 'selected'; ?>>Available</option>
                        <option value="Not Available" <?php if(($edit_data['status']??'')=='Not Available') echo 'selected'; ?>>Not Available</option>
                    </select>
                </div>
                <div class="form-group full-width"><label>Room Description</label>
                    <textarea name="description" rows="2"><?php echo $edit_data['description'] ?? ''; ?></textarea>
                </div>
                <div class="form-group" style="grid-column: span 2;"><label>Upload Image</label>
                    <input type="file" name="room_image">
                </div>
                <div class="form-group" style="grid-column: span 2;"><label>Filename (Optional)</label>
                    <input type="text" name="image_filename_manual" value="<?php echo $edit_data['image'] ?? ''; ?>">
                </div>

                <button type="submit" name="save_room" class="btn-action">
                    <?php echo isset($edit_data) ? 'Update Room Details' : 'Add New Room'; ?>
                </button>
            </form>
        </div>

        <div class="glass-panel" style="padding: 0; overflow: hidden;">
            <table>
                <thead style="background: rgba(255,255,255,0.05);">
                    <tr>
                        <th style="padding-left: 25px;">Image</th>
                        <th>Room Identity</th>
                        <th>Price</th>
                        <th>Rating</th>
                        <th>Status Toggle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $rooms_query->fetch_assoc()): ?>
                    <tr>
                        <td style="padding-left: 25px;">
                            <img src="../rooms/<?php echo $r['image']; ?>" class="room-img" onerror="this.src='https://placehold.co/100x100?text=Room'">
                        </td>
                        <td><b><?php echo $r['room_name']; ?></b></td>
                        <td style="color: #2ecc71; font-weight: 800;">₱<?php echo number_format($r['price']); ?></td>
                        <td><i class="fa-solid fa-star" style="color:#f1c40f;"></i> <?php echo $r['rating']; ?></td>
                        <td>
                            <a href="?toggle_status=<?php echo $r['id']; ?>&current=<?php echo $r['status']; ?>" 
                               class="status-pill status-<?php echo str_replace(' ', '-', $r['status']); ?>">
                               <?php echo $r['status']; ?>
                            </a>
                        </td>
                        <td>
                            <a href="?edit=<?php echo $r['id']; ?>" style="color: #888; margin-right: 15px;"><i class="fa-solid fa-pen"></i></a>
                            <a href="?delete=<?php echo $r['id']; ?>" style="color: #c62828;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>