<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | StayHub</title>
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

        /* --- ABOUT HEADER BANNER --- */
        .about-header-banner {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 40px 0;
            width: 100%;
        }

        .about-header-banner h1 {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* --- ABOUT CONTENT --- */
        .about-section {
            padding: 80px 8%;
            background: #fff;
        }

        .about-container {
            display: flex;
            align-items: center;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-text {
            flex: 1.2;
        }

        .about-text h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #0076a3;
            display: inline-block;
            border-bottom: 4px solid #f37021;
        }

        .about-text p {
            font-size: 17px;
            color: #444;
            margin-bottom: 25px;
            text-align: justify;
        }

        /* ZOOM EFFECT STYLES */
        .about-image {
            flex: 1;
            overflow: hidden; /* Clips the zoom */
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .about-image img {
            width: 100%;
            display: block;
            transition: transform 0.5s ease; /* Smooth transition */
        }

        .about-image:hover img {
            transform: scale(1.1); /* Zoom in on hover */
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

        .footer-col p, .footer-col li {
            font-size: 15px;
            color: #ccc;
            margin-bottom: 15px;
            list-style: none;
        }

        .footer-col a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-col a:hover { color: #f37021; }

        .newsletter-box {
            display: flex;
            background: white;
            border-radius: 6px;
            padding: 5px;
            margin-top: 20px;
        }

        .newsletter-box input {
            border: none;
            padding: 12px;
            flex: 1;
            outline: none;
            font-size: 15px;
            color: #333;
        }

        .newsletter-box button {
            background: transparent;
            color: #f37021;
            border: none;
            font-weight: 800;
            padding: 0 20px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .copyright {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 30px;
            font-size: 15px;
            color: #666;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 1100px) {
            .about-container { flex-direction: column; text-align: center; }
        }

        @media (max-width: 700px) {
            header { flex-direction: column; gap: 15px; padding: 15px; }
            .brand-title { font-size: 32px; }
        }
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
                <li><a href="about.php" class="nav-link active">ABOUT</a></li>
                <li><a href="contacts.php" class="nav-link">CONTACTS</a></li>
                <li><a href="login.php" class="nav-link">LOGIN</a></li>
                <li><a href="register.php" class="nav-link">SIGN UP</a></li>
            </ul>
        </nav>
    </header>

    <div class="about-header-banner">
        <h1>ABOUT US</h1>
    </div>

    <section class="about-section">
        <div class="about-container">
            <div class="about-text">
                <h2>Our Mission</h2>
                <p>Welcome to StayHub! We provide a seamless and secure way to book hotel rooms for your travels. Our mission is to make your stay comfortable and stress-free, with easy booking, reliable customer service, and transparent pricing.</p>
                <p>Whether you are traveling for business or leisure, StayHub is your trusted companion for all your accommodation needs. We take pride in partnering with the best resorts to bring you luxury at your fingertips.</p>
            </div>
            <div class="about-image">
                <img src="pic.jpg" alt="StayHub Resort View">
            </div>
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
                    <input type="email" placeholder="Enter your email">
                    <button>SUBSCRIBE</button>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>© 2026 all Rights Reserved</p>
        </div>
    </footer>

</body>
</html>