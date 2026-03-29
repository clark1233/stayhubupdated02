<?php
session_start();

// Database connection
$conn = new mysqli("localhost","root","","stayhub");
if ($conn->connect_error) die("DB Connection failed");

// ---------- Default admin creation ----------
$checkAdmin = $conn->query("SELECT id FROM users WHERE role='admin'");
if ($checkAdmin->num_rows == 0) {
    $conn->query("INSERT INTO users (name,email,password,role,status) VALUES 
        ('Admin','admin@stayhub.com',SHA2('admin123',256),'admin','active')");
}

// ---------- Login logic ----------
$error = "";

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=SHA2(?,256)");
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows==1) {
        $user = $result->fetch_assoc();

        if ($user['status']=="banned") {
            $error = "Your account is currently restricted.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role']=="admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: customer/dashboard.php");
            }
            exit();
        }
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StayHub</title>
    <style>
        /* --- GLOBAL & NAVIGATION (EXACT SYNC WITH SIGNUP) --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f9f9f9; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }
        
        header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 10px 5%; 
            background: #fff; 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }

        .brand-container { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-container img { height: 80px; width: auto; }
        
        .brand-title { 
            font-size: 38px; 
            font-weight: 800; 
            color: #0076a3; 
            letter-spacing: -1.5px; 
        }
        .brand-title span { color: #f37021; }

        nav ul { display: flex; list-style: none; gap: 40px; }
        .nav-link { 
            text-decoration: none; 
            color: #333; 
            font-weight: 800; 
            font-size: 18px; 
            text-transform: uppercase; 
            transition: 0.3s; 
        }
        .nav-link:hover, .nav-link.active { color: #f37021; }

        /* --- LOGIN CARD (PROFESSIONAL GLASS STYLE) --- */
        .auth-wrapper { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 80px 5%; 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('pic.jpg'); 
            background-size: cover; 
            background-position: center; 
        }

        .auth-card { 
            background: rgba(255, 255, 255, 0.08); 
            backdrop-filter: blur(15px); 
            padding: 50px 40px; 
            border-radius: 20px; 
            color: white; 
            width: 100%; 
            max-width: 420px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.4); 
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .auth-card h2 { 
            margin-bottom: 35px; 
            font-size: 28px; 
            font-weight: 800;
            color: #fff;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .error { 
            background: rgba(231, 76, 60, 0.2); 
            color: #ff9999; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            font-size: 13px;
            border: 1px solid rgba(231, 76, 60, 0.4);
        }

        .form-group { margin-bottom: 25px; text-align: left; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 10px; color: #bbb; text-transform: uppercase; letter-spacing: 1px; }
        
        .form-group input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 10px; 
            font-size: 15px; 
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            color: #222;
            transition: 0.3s;
        }

        .form-group input:focus {
            background: #fff;
            box-shadow: 0 0 0 4px rgba(243, 112, 33, 0.3);
            border-color: #f37021;
        }
        
        .btn-auth { 
            background: #f37021; 
            color: white; 
            border: none; 
            width: 100%; 
            padding: 16px; 
            border-radius: 12px; 
            font-weight: 800; 
            font-size: 16px; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 10px; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-auth:hover { background: #e65c00; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(243, 112, 33, 0.3); }
        
        .auth-footer { text-align: center; margin-top: 30px; font-size: 14px; color: #ccc; }
        .auth-footer a { color: #f37021; text-decoration: none; font-weight: 700; }
        .auth-footer a:hover { text-decoration: underline; }

        /* --- FOOTER (MATCHING DESIGN) --- */
        footer { background: #1a1a1a; color: white; padding: 80px 8% 30px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 50px; margin-bottom: 50px; }
        .footer-col h3 { font-size: 22px; margin-bottom: 30px; border-bottom: 3px solid #f37021; display: inline-block; padding-bottom: 8px; }
        .footer-col p, .footer-col li { font-size: 15px; color: #ccc; margin-bottom: 15px; list-style: none; }
        .footer-col a { color: #ccc; text-decoration: none; transition: 0.3s; }
        .footer-col a:hover { color: #f37021; }

        .copyright { text-align: center; border-top: 1px solid #333; padding-top: 30px; font-size: 15px; color: #666; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="brand-container">
            <img src="indexlogo.png" alt="StayHub Logo">
            <h1 class="brand-title">Stay<span>Hub</span></h1>
        </a>
        <nav>
            <ul>
                <li><a href="index.php" class="nav-link">HOME</a></li>
                <li><a href="about.php" class="nav-link">ABOUT</a></li>
                <li><a href="contacts.php" class="nav-link">CONTACTS</a></li>
                <li><a href="login.php" class="nav-link active">LOGIN</a></li>
                <li><a href="signup.php" class="nav-link">SIGN UP</a></li>
            </ul>
        </nav>
    </header>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Access Account</h2>
            
            <?php if($error != ""): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-auth">Sign In</button>
            </form>
            
            <p class="auth-footer">
                New to StayHub? <a href="signup.php">Create an account</a>
            </p>
        </div>
    </div>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Contact US</h3>
                <p>📍 Minglanilla, Cebu</p>
                <p>📞 +0931 909 5269</p>
                <p>📧 stayhub@gmail.com</p>
            </div>
            <div class="footer-col">
                <h3>Menu Link</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contacts.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Stay Connected</h3>
                <p style="font-size: 14px; color: #bbb; margin-top: 10px;">Login to manage your bookings.</p>
            </div>
        </div>
        <div class="copyright">
            <p>© 2026 StayHub. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>