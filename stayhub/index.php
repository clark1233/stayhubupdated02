<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayHub | Luxury Hotel Booking</title>
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

        /* --- NAVIGATION --- */
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

        /* --- HERO SECTION WITH GLASSMORPHISM --- */
        .hero {
            height: 550px;
            background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('pic.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            padding: 0 8%;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 35px;
            border-radius: 15px;
            color: white;
            width: 100%;
            max-width: 320px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .booking-card h3 {
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 13px; margin-bottom: 5px; color: #eee; }
        .input-group input { 
            width: 100%; 
            padding: 10px; 
            border: none; 
            border-radius: 6px; 
            font-size: 15px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
        }

        .btn-orange {
            background: #f37021;
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            display: block;
            text-decoration: none;
        }

        .btn-orange:hover { background: #d35400; }

        /* --- ROOM SLIDESHOW SECTION --- */
        .rooms-section {
            padding: 60px 5%;
            background: #fff;
            text-align: center;
        }

        .rooms-section h2 {
            font-size: 38px;
            margin-bottom: 40px;
            display: inline-block;
            border-bottom: 5px solid #f37021;
        }

        .slider-container {
            position: relative;
            overflow: hidden;
            max-width: 1300px;
            margin: 0 auto;
        }

        .room-slider {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            gap: 25px;
            padding: 20px 0;
        }

        .room-card {
            min-width: calc(33.333% - 25px);
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: left;
            cursor: pointer;
            transition: 0.3s ease;
        }
        
        .room-card:hover { transform: translateY(-5px); }

        .room-card img { width: 100%; height: 230px; object-fit: cover; }
        .room-info { padding: 20px; }
        .room-price { color: #f37021; font-size: 22px; font-weight: 800; }
        .room-rating { color: #ffb400; margin: 5px 0 10px; }
        .room-comment { font-size: 14px; color: #666; font-style: italic; border-left: 3px solid #f37021; padding-left: 10px; height: 45px; overflow: hidden; }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.6);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            cursor: pointer;
            z-index: 10;
            border-radius: 50%;
        }

        .prev { left: 5px; }
        .next { right: 5px; }

        /* --- ROOM DETAILS MODAL --- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none; 
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background: #fff;
            width: 90%;
            max-width: 850px;
            border-radius: 20px;
            display: flex;
            overflow: hidden;
            position: relative;
            animation: modalFade 0.3s ease;
        }
        @keyframes modalFade { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .modal-left { flex: 1.2; }
        .modal-left img { width: 100%; height: 100%; object-fit: cover; min-height: 400px; }
        .modal-right { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 30px; cursor: pointer; color: #333; font-weight: bold; }
        .modal-right h2 { color: #0076a3; font-size: 32px; margin-bottom: 10px; font-weight: 800; }
        .modal-price-val { color: #f37021; font-size: 26px; font-weight: 800; margin-bottom: 15px; }
        .modal-desc { margin-bottom: 30px; font-size: 15px; color: #666; line-height: 1.6; text-align: justify; }
        .btn-modal-book {
            background: #f37021;
            color: white;
            padding: 18px;
            border-radius: 35px;
            text-align: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 18px;
            transition: 0.3s;
        }
        .btn-modal-book:hover { background: #0076a3; }

        /* --- ABOUT & FOOTER --- */
        .about-bottom { padding: 80px 8%; background-color: #f9f9f9; }
        .about-container { display: flex; align-items: center; gap: 60px; }
        .about-content { flex: 1; }
        .about-content h2 { font-size: 38px; margin-bottom: 20px; display: inline-block; border-bottom: 5px solid #f37021; }
        .about-content p { margin-bottom: 20px; text-align: justify; }

        .btn-black {
            background: #000;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 35px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-black:hover { background: #333; }

        .about-image { flex: 1; overflow: hidden; border-radius: 12px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .about-image img { width: 100%; display: block; transition: transform 0.5s ease; }
        .about-image:hover img { transform: scale(1.1); }

        footer { background: #1a1a1a; color: white; padding: 80px 8% 30px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 50px; margin-bottom: 50px; }
        .footer-col h3 { font-size: 22px; margin-bottom: 30px; border-bottom: 3px solid #f37021; display: inline-block; padding-bottom: 8px; }
        .footer-col p, .footer-col li { font-size: 15px; color: #ccc; margin-bottom: 15px; list-style: none; }
        .footer-col a { color: #ccc; text-decoration: none; transition: color 0.3s; }
        .footer-col a:hover { color: #f37021; }

        .newsletter-box { display: flex; background: white; border-radius: 6px; padding: 5px; margin-top: 20px; }
        .newsletter-box input { border: none; padding: 12px; flex: 1; outline: none; font-size: 15px; }
        .newsletter-box button { background: transparent; color: #f37021; border: none; font-weight: 800; padding: 0 20px; cursor: pointer; text-transform: uppercase; }

        .copyright { text-align: center; border-top: 1px solid #333; padding-top: 30px; font-size: 15px; color: #666; }

        @media (max-width: 1100px) {
            .room-card { min-width: calc(50% - 25px); }
            .about-container { flex-direction: column; text-align: center; }
        }
        @media (max-width: 768px) {
            .modal-content { flex-direction: column; }
            .room-card { min-width: 100%; }
            header { flex-direction: column; gap: 15px; padding: 15px; }
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
                <li><a href="index.php" class="nav-link active">HOME</a></li>
                <li><a href="about.php" class="nav-link">ABOUT</a></li>
                <li><a href="contacts.php" class="nav-link">CONTACTS</a></li>
                <li><a href="login.php" class="nav-link">LOGIN</a></li>
                <li><a href="signup.php" class="nav-link">SIGN UP</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="booking-card">
            <h3>BOOK A ROOM ONLINE</h3>
            <form action="login.php" method="GET">
                <div class="input-group">
                    <label>Arrival Date</label>
                    <input type="date" name="arrival" required>
                </div>
                <div class="input-group">
                    <label>Departure Date</label>
                    <input type="date" name="departure" required>
                </div>
                <button type="submit" class="btn-orange">Book Now</button>
            </form>
        </div>
    </section>

    <section class="rooms-section">
        <h2>OUR BEDROOMS</h2>
        <div class="slider-container">
            <button class="slider-btn prev" onclick="moveSlide(-1)">&#10094;</button>
            <div class="room-slider" id="slider">
                
                <div class="room-card" onclick="showDetails('room1.jpg', 'Executive Suite', '₱3,500', 'Experience unparalleled comfort in our Executive Suite.')">
                    <img src="room1.jpg" alt="Room 1">
                    <div class="room-info"><p class="room-price">₱3,500/night</p><div class="room-rating">★★★★★ 5.0</div><p class="room-comment">"Amazing view and very clean!"</p></div>
                </div>

                <div class="room-card" onclick="showDetails('room2.jpg', 'Deluxe Room', '₱2,800', 'Perfect for couples. Our Deluxe Room combines modern interior design.')">
                    <img src="room2.jpg" alt="Room 2">
                    <div class="room-info"><p class="room-price">₱2,800/night</p><div class="room-rating">★★★★☆ 4.5</div><p class="room-comment">"Very comfortable beds, loved it."</p></div>
                </div>

                <div class="room-card" onclick="showDetails('room3.jpg', 'Standard Cozy', '₱1,500', 'An affordable yet elegant choice for solo travelers.')">
                    <img src="room3.jpg" alt="Room 3">
                    <div class="room-info"><p class="room-price">₱1,500/night</p><div class="room-rating">★★★★☆ 4.0</div><p class="room-comment">"Great value for money."</p></div>
                </div>

                <div class="room-card" onclick="showDetails('room4.jpg', 'Luxury Penthouse', '₱4,200', 'The peak of luxury with private terrace.')">
                    <img src="room4.jpg" alt="Room 4">
                    <div class="room-info"><p class="room-price">₱4,200/night</p><div class="room-rating">★★★★★ 5.0</div><p class="room-comment">"Luxury at its finest!"</p></div>
                </div>

                <div class="room-card" onclick="showDetails('room5.jpg', 'Skyline View', '₱3,100', 'Floor-to-ceiling windows and private balcony.')">
                    <img src="room5.jpg" alt="Room 5">
                    <div class="room-info"><p class="room-price">₱3,100/night</p><div class="room-rating">★★★★☆ 4.7</div><p class="room-comment">"The balcony view was breathtaking."</p></div>
                </div>
            </div>
            <button class="slider-btn next" onclick="moveSlide(1)">&#10095;</button>
        </div>
    </section>

    <div class="modal-overlay" id="detailsModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeDetails()">&times;</span>
            <div class="modal-left">
                <img src="" id="m-img">
            </div>
            <div class="modal-right">
                <h2 id="m-title">Room Name</h2>
                <div class="modal-price-val" id="m-price">Price</div>
                <p class="modal-desc" id="m-desc">Description</p>
                <a href="login.php" class="btn-modal-book">Book This Room</a>
            </div>
        </div>
    </div>

    <section class="about-bottom">
        <div class="about-container">
            <div class="about-content">
                <h2>ABOUT US</h2>
                <p>Welcome to StayHub! We provide a seamless and secure way to book hotel rooms for your travels.</p>
                <p>Whether you are traveling for business or leisure, StayHub is your trusted companion.</p>
                <a href="about.php" class="btn-black">READ MORE</a>
            </div>
            <div class="about-image"><img src="pic2.png" alt="StayHub Resort"></div>
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
                    <li><a href="contacts.php">Contact Us</a></li>
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
            <p>© 2026 StayHub. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        let currentIndex = 0;
        const slider = document.getElementById('slider');
        const cards = document.querySelectorAll('.room-card');
        const totalCards = cards.length;

        function moveSlide(direction) {
            const gap = 25;
            const cardWidth = (cards[0].offsetWidth || 350) + gap;
            let visibleCards = window.innerWidth > 1100 ? 3 : (window.innerWidth > 700 ? 2 : 1);
            
            currentIndex += direction;
            
            if (currentIndex < 0) currentIndex = totalCards - visibleCards;
            if (currentIndex > totalCards - visibleCards) currentIndex = 0;

            slider.style.transform = `translateX(${-currentIndex * cardWidth}px)`;
        }

        setInterval(() => moveSlide(1), 4000);

        function showDetails(img, title, price, desc) {
            document.getElementById('m-img').src = img;
            document.getElementById('m-title').innerText = title;
            document.getElementById('m-price').innerText = price + " / night";
            document.getElementById('m-desc').innerText = desc;
            document.getElementById('detailsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDetails() {
            document.getElementById('detailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('detailsModal')) closeDetails();
        }
    </script>
</body>
</html>