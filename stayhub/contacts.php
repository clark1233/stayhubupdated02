<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | StayHub</title>
    <style>
        /* --- RESET & GLOBAL STYLES --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        /* --- SLIM & STICKY NAVIGATION --- */
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

        .brand-container {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-container img {
            height: 80px; 
            width: auto;
            display: block;
        }

        .brand-title {
            font-size: 38px;
            font-weight: 800;
            color: #0076a3;
            letter-spacing: -1.5px;
        }

        .brand-title span {
            color: #f37021;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        .nav-link {
            text-decoration: none;
            color: #333;
            font-weight: 800;
            font-size: 18px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #f37021;
        }

        /* --- HEADER BANNER --- */
        .contact-header-banner {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 40px 0;
            width: 100%;
        }

        .contact-header-banner h1 {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* --- CONTACT SECTION --- */
        .contact-section {
            padding: 60px 8%;
            background: #fff;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-info h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #0076a3;
            display: inline-block;
            border-bottom: 4px solid #f37021;
        }

        .contact-info p {
            font-size: 17px;
            margin-bottom: 30px;
            color: #555;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 18px;
        }

        /* CONTACT FORM */
        .contact-form {
            background: #fdfdfd;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn-send {
            background: #f37021;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .btn-send:hover { background: #d35400; }

        /* --- MAP SECTION --- */
        .map-section {
            padding: 0 8% 60px;
            background: #fff;
            text-align: center;
        }

        .map-section h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #0076a3;
            display: inline-block;
            border-bottom: 4px solid #f37021;
        }

        .map-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* --- FOOTER --- */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 80px 8% 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 50px;
        }

        .footer-col h3 {
            font-size: 22px;
            margin-bottom: 30px;
            border-bottom: 3px solid #f37021;
            display: inline-block;
            padding-bottom: 8px;
        }

        .footer-col p, .footer-col li { font-size: 15px; color: #ccc; margin-bottom: 15px; list-style: none; }
        .footer-col a { color: #ccc; text-decoration: none; transition: color 0.3s; }
        .footer-col a:hover { color: #f37021; }

        .newsletter-box {
            display: flex;
            background: white;
            border-radius: 6px;
            padding: 5px;
            margin-top: 20px;
        }

        .newsletter-box input { border: none; padding: 12px; flex: 1; outline: none; font-size: 15px; }
        .newsletter-box button { background: transparent; color: #f37021; border: none; font-weight: 800; padding: 0 20px; cursor: pointer; }

        .copyright {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 30px;
            font-size: 15px;
            color: #666;
        }

        @media (max-width: 900px) { .contact-container { grid-template-columns: 1fr; } }
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
                <li><a href="contact.php" class="nav-link active">CONTACTS</a></li>
                <li><a href="login.php" class="nav-link">LOGIN</a></li>
                <li><a href="register.php" class="nav-link">SIGN UP</a></li>
            </ul>
        </nav>
    </header>

    <div class="contact-header-banner">
        <h1>CONTACT US</h1>
    </div>

    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about our rooms or your booking? Send us a message and our team will get back to you within 24 hours.</p>
                
                <div class="info-item"><span>📍</span> Minglanilla, Cebu, Philippines</div>
                <div class="info-item"><span>📞</span> +0931 909 5269</div>
                <div class="info-item"><span>📧</span> stayhub@gmail.com</div>
            </div>

            <div class="contact-form">
                <form action="contact_process.php" method="POST">
                    <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Message</label><textarea name="message"></textarea></div>
                    <button type="submit" class="btn-send">SEND MESSAGE</button>
                </form>
            </div>
        </div>
    </section>

    <section class="map-section">
        <h2>Our Location</h2>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62812.86877073458!2d123.75389659021487!3d10.241088710373286!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a99268598c608d%3A0xd2016057b1f9cd28!2sMinglanilla%2C%20Cebu!5e0!3m2!1sen!2sph!4v1709564283120!5m2!1sen!2sph" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

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
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>News Letter</h3>
                <div class="newsletter-box">
                    <input type="email" placeholder="Enter your email"><button>SUBSCRIBE</button>
                </div>
            </div>
        </div>
        <div class="copyright"><p>© 2026 all Rights Reserved</p></div>
    </footer>

</body>
</html>