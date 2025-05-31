<?php
// filepath: c:\xampp\htdocs\Prompting\about.php

// Include the database connection if needed
include 'db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user profile picture
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'profile.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #ffc300;
            --secondary-color: forestgreen;
            --accent-yellow: #ffdd57;
            --accent-green: #a0c334;
            --background-light: #f3f4f6;
            --background-gradient: linear-gradient(135deg, #a0c334, #ffc300);
        }

        body {
            font-family: '', sans-serif;
            background-color: var(--background-light);
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0e2f1c;
            padding: 10px 20px;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .nav-icons {
            display: flex;
            gap: 20px;
        }

        .navbar .nav-icons a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            position: relative;
        }

        .navbar .nav-icons a:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
        }

        .navbar .nav-icons a:hover {
            color: #f5cc59;
        }

        .navbar .profile-menu {
            position: relative;
        }

        .navbar .profile-menu .profile-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            overflow: hidden;
        }

        .navbar .profile-menu .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .navbar .profile-menu .dropdown {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            color: black;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1000;
        }

        .navbar .profile-menu .dropdown a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }

        .navbar .profile-menu .dropdown a:hover {
            background-color: #f5f5f5;
        }

        .navbar .profile-menu.active .dropdown {
            display: block;
        }

        .about-section {
            padding: 6rem 2rem;
            text-align: center;
            background-color: #f7d16c;
            color: white;
        }

        .creators-section {
            padding: 6rem 2rem;
            text-align: center;
            background-color: var(--secondary-color);
            color: white;
        }

        .creators-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
            justify-items: center;
        }

        .creator-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            width: 100%;
            max-width: 300px;
        }

        .creator-container:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .creator-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .creator-container:hover img {
            opacity: 0.8;
            transform: scale(1.1);
        }

        .creator-details {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .creator-container:hover .creator-details {
            opacity: 1;
            transform: scale(1);
        }

        .creator-details h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .creator-details p {
            font-size: 1rem;
            margin: 0.2rem 0;
        }
            body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    .footer {
      background-color: #1f5a22;
      color: #fff6db;
      padding: 40px 20px;
      margin-top: 50px;
    }

  .footer-container {
  max-width: 1200px;
  margin: auto;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 60px;
}

    .footer-section {
      flex: 1 1 250px;
    }

    .footer h3 {
      margin-bottom: 15px;
      color: #ffda8e;
    }

    .footer p, .footer a {
      color: #fff6db;
      text-decoration: none;
      line-height: 1.6;
      font-size: 15px;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 30px;
      border-top: 1px solid #fff6db33;
      padding-top: 15px;
      font-size: 14px;
    }
    @media (max-width: 768px) {
      .footer-container {
        flex-direction: column;
        align-items: center;
      }
    }

    .cart-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: 400px;
        height: 100vh;
        background: #0F3C2C;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: transform 0.3s ease, visibility 0.3s ease, opacity 0.3s ease;
        padding: 20px;
        transform: translateX(100%);
        visibility: hidden;
        opacity: 0;
        overflow-y: auto;
        color: white;
        border-radius: 15px 0 0 15px;
        display: flex;
        flex-direction: column;
    }

    .cart-drawer.open {
        transform: translateX(0);
        visibility: visible;
        opacity: 1;
    }

    .cart-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .cart-overlay.active {
        visibility: visible;
        opacity: 1;
    }

    /* Notification Drawer Styles */
    .notification-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: 400px;
        height: 100vh;
        background: #0F3C2C;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: transform 0.3s ease, visibility 0.3s ease, opacity 0.3s ease;
        padding: 20px;
        transform: translateX(100%);
        visibility: hidden;
        opacity: 0;
        overflow-y: auto;
        color: white;
        border-radius: 15px 0 0 15px;
        display: flex;
        flex-direction: column;
    }

    .notification-drawer.open {
        transform: translateX(0);
        visibility: visible;
        opacity: 1;
    }

    .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .notification-overlay.active {
        visibility: visible;
        opacity: 1;
    }

    .notification-item {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 3px solid #D5AD50;
        transition: transform 0.2s ease, background 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        transform: translateX(-5px);
        background: rgba(255, 255, 255, 0.1);
    }

    .notification-item h3 {
        color: #D5AD50;
        margin: 0 0 5px 0;
        font-size: 16px;
    }

    .notification-item p {
        color: white;
        margin: 5px 0;
        font-size: 14px;
        line-height: 1.4;
    }

    .notification-item .timestamp {
        color: #D5AD50;
        font-size: 12px;
        margin-top: 5px;
    }

    .notification-item.unread {
        border-left: 3px solid #D5AD50;
    }

    .notification-item.read {
        border-left: 3px solid rgba(213, 173, 80, 0.3);
    }
  </style>
</head>
</body>
    </style>
    <script>
        // Profile dropdown toggle
        function toggleProfileMenu() {
            const profileMenu = document.querySelector('.profile-menu');
            profileMenu.classList.toggle('active');
        }

        // Close profile menu if clicked outside
        document.addEventListener('click', function (event) {
            const profileMenu = document.querySelector('.profile-menu');
            if (!profileMenu.contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        });
    </script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <!-- Left Logo/Brand (Optional) -->
        <div style="color: #f5cc59; font-weight: bold; font-size: 1.2rem;">iPawnshop</div>

        <!-- Right Navigation -->
        <div style="display: flex; align-items: center; gap: 30px;">
            <!-- Text Navigation -->
            <a href="home.php" style="color: white; text-decoration: none; font-size: 16px;">Home</a>
            <a href="market.php" style="color: white; text-decoration: none; font-size: 16px;">Marketplace</a>
            <a href="branch.php" style="color: white; text-decoration: none; font-size: 16px;">Branches</a>
            <a href="about.php" style="color: white; text-decoration: none; font-size: 16px;">About Us</a>

            <!-- Cart Icon -->
            <a href="javascript:void(0)" onclick="toggleCart()" style="color: white; font-size: 20px;" title="Cart">🛒</a>

            <!-- Notification Icon -->
            <div style="position: relative;">
                <a href="javascript:void(0)" onclick="openNotifications()" style="color: white; font-size: 20px;" title="Notifications">🔔</a>
                <span class="notification-count" style="position: absolute; top: -8px; right: -8px; background: #D5AD50; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;">0</span>
            </div>

            <!-- SANGLA NOW Button -->
            <button onclick="window.location.href='sangla.php'" style="background-color: #f5cc59; color: #0e2f1c; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                SANGLA NOW
            </button>

            <!-- Profile Menu -->
            <div class="profile-menu">
                <div class="profile-icon" onclick="toggleProfileMenu()">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
                </div>
                <div class="dropdown">
                    <a href="profile.php">Profile</a>
                    <a href="sangla.php">Sangla</a>
                    <a href="sanglarequests.php">Sangla Requests</a>
                    <a href="tubo.php">Tubo</a>
                    <a href="tubos.php">Tubos</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Drawer -->
    <div class="notification-drawer" id="notificationDrawer">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #D5AD50;">Notifications</h2>
            <button onclick="closeNotifications()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #D5AD50;">&times;</button>
        </div>
        <div id="notificationItems" style="flex: 1; overflow-y: auto; padding-right: 10px;">
            <!-- Notifications will be added here dynamically -->
        </div>
    </div>
    <div class="notification-overlay" id="notificationOverlay" onclick="closeNotifications()"></div>

    <!-- About Us Section -->
    <section class="about-section">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; max-width: 1200px; margin: 0 auto;">
            <!-- Description -->
            <div style="flex: 1; text-align: left;">
                <h1 class="text-5xl font-extrabold mb-6">About Us</h1>
                <p class="text-lg leading-relaxed">
                    Welcome to iPawnshop, your modern and secure digital partner in pawn transactions. We are committed to transforming traditional pawnshop operations into a seamless, transparent, and customer-friendly experience. Our system empowers both customers and staff with real-time tracking, efficient item management, secure loan processing, and easy-to-use interfaces.At iPawnshop, we value trust, security, and innovation. Whether you're pawning, redeeming, or checking transaction history, we're here to make your journey simple and secure. Let's make smart pawning accessible to everyone—digitally.
                </p>
            </div>
            <!-- Logo Image -->
            <div style="flex: 1; text-align: center; position: relative; top: -20px;">
                <img src="images/about.png" alt="iPawnshop Logo" style="max-width: 100%; height: auto; margin-left: 125px;">
            </div>
        </div>
    </section>

  <!-- Our Values and Our History Section -->
<section style="background-color: var(--accent-yellow); padding: 4rem 2rem; color: #333;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: row; gap: 4rem; flex-wrap: wrap;">
        <!-- Our History - Left Side -->
        <div style="flex: 1; text-align: left;">
            <h2 class="text-4xl font-semibold mb-4">Our History</h2>
            <p class="text-lg leading-relaxed max-w-xl">
                Founded in the heart of our community, iPawnshop began as a small family-owned venture dedicated to serving local needs. 
                Over the years, we've evolved from a traditional brick-and-mortar pawnshop into a cutting-edge digital platform. 
                While technology has changed how we operate, our commitment to people remains unchanged. 
                We're proud of our heritage and excited for the future.
            </p>
        </div>

        <!-- Our Values - Right Side -->
        <div style="flex: 1; text-align: left;">
            <h2 class="text-4xl font-semibold mb-4">Our Values</h2>
            <p class="text-lg leading-relaxed max-w-xl ml-auto">
                At iPawnshop, we stand on the pillars of integrity, customer-first service, and technological innovation. 
                We believe in building lasting relationships founded on trust, transparency, and respect. Our goal is to empower every customer 
                through responsible financial solutions that are accessible, secure, and modern.
            </p>
        </div>
    </div>
</section>


    <!-- Contact and Social Media Section -->
<section class="contact-social-section" style="background-color: var(--background-light); padding: 4rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 4rem; max-width: 1200px; margin: 0 auto; flex-wrap: wrap;">
        
        <!-- Contact Details -->
        <div style="flex: 1; text-align: left;">
            <h2 class="text-3xl font-semibold mb-4">Contact Details</h2>
            <p><strong>Phone:</strong> 0907-536-5447</p>
            <p><strong>Email:</strong> support@ipawnshop.com</p>
            <p><strong>Address:</strong> 123 Pawnshop Lane, Philippines</p>
            <p><strong>Working Hours:</strong> Mon-Fri, 9:00 AM - 6:00 PM</p>
        </div>

        <!-- Follow Us on Social Media -->
        <div style="flex: 1; text-align: left;">
            <h2 class="text-3xl font-semibold mb-4">Follow Us on Social Media</h2>
            <p>Stay connected with us:</p>
            <ul style="list-style: none; padding: 0;">
                <li><a href="https://facebook.com" target="_blank" style="color: var(--primary-color); text-decoration: none;">Facebook</a></li>
                <li><a href="https://twitter.com" target="_blank" style="color: var(--primary-color); text-decoration: none;">Twitter</a></li>
                <li><a href="https://instagram.com" target="_blank" style="color: var(--primary-color); text-decoration: none;">Instagram</a></li>
            </ul>
        </div>
    </div>
</section>


    <!-- Creators Section -->
    <section class="creators-section">
        <h2 class="text-4xl font-semibold">Meet the Creators</h2>
        <div class="creators-grid">
            <!-- Creator 1 -->
            <div class="creator-container">
                <img src="images/Bea.jpg" alt="Creator 1">
                <div class="creator-details">
                    <h3>Bea Ramos</h3>
                    <p>Contact: 09152607190</p>
                    <p>Email: bearamos788@gmail.com</p>
                </div>
            </div>
            <!-- Creator 2 -->
            <div class="creator-container">
                <img src="images/Jed.jpg" alt="Creator 2">
                <div class="creator-details">
                    <h3>Jed Ismael Polong</h3>
                    <p>Contact: 09958433592</p>
                    <p>Email: polongjed26@gmail.com</p>
                </div>
            </div>
            <!-- Creator 3 -->
            <div class="creator-container">
                <img src="images/cy.jpg" alt="Creator 3">
                <div class="creator-details">
                    <h3>Cyrel Bucad</h3>
                    <p>Contact: 09075365447 </p>
                    <p>Email: bucadcyrel@gmail.com</p>
                </div>
            </div>
            <!-- Creator 4 -->
            <div class="creator-container">
                <img src="images/Alexa.jpg" alt="Creator 4">
                <div class="creator-details">
                    <h3>Alexxa Concepcion</h3>
                    <p>Contact: 09637265441</p>
                    <p>Email: alexaconcepcion52@gmail.com</p>
                </div>
            </div>
            <!-- Creator 5 -->
            <div class="creator-container">
                <img src="images/DAISY.jpg" alt="Creator 5">
                <div class="creator-details">
                    <h3>Daisy Barce</h3>
                    <p>Contact: 09469522764</p>
                    <p>Email: daisyreibarce2@gmail.com</p>
                </div>
            </div>
            <!-- Creator 6 -->
            <div class="creator-container">
                <img src="images/Jhana.jpg" alt="Creator 6">
                <div class="creator-details">
                    <h3>Jhana Loraine Raquid</h3>
                    <p>Contact: 09453570432</p>
                    <p>Email: jhanalorainegomezraquid@gmail.com</p>
                </div>
            </div>
            <!-- Empty Slots for 3x3 Grid -->
            <div class="creator-container"></div>
            <div class="creator-container"></div>
            <div class="creator-container"></div>
        </div>
    </section>
    
<footer class="footer">
  <div class="footer-container">
    
   <!-- Logo Section -->
    <div class="footer-section" style="flex: 1; display: flex; justify-content: flex-start;">
     <img src="images/footer.png" alt="Footer Logo" style="width: 150px; height: auto;">
    </div>


    <!-- About Us -->
    <div class="footer-section" style="flex: 1;">
      <h3>About Us</h3>
      <p>Trusted Pawnshop since 2025. We provide fair and fast loan services secured with valuable collaterals.</p>
    </div>

    <!-- Quick Links -->
    <div class="footer-section" style="flex: 1;">
      <h3>Quick Links</h3>
      <p><a href="home.php">Home</a></p>
      <p><a href="marketplace.php">Marketplace</a></p>
      <p><a href="branch.php">Branch</a></p>
      <p><a href="about.php">About Us</a></p>
    </div>

    <!-- Contact Info -->
    <div class="footer-section" style="flex: 1;">
      <h3>Contact Info</h3>
      <p>Email: support@ipawnshop.com</p>
      <p>Phone: 0907-536-5447</p>
      <p>Address: 123 iPawnshop Lane, Philippines</p>
    </div>

    <!-- Business Hours -->
    <div class="footer-section" style="flex: 1;">
      <h3>Business Hours</h3>
      <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
      <p>Saturday: 9:00 AM - 4:00 PM</p>
      <p>Sunday: Closed</p>
    </div>

  </div>

  <div class="footer-bottom">
    &copy; 2025 iPawnshop. All rights reserved.
  </div>
</footer>

<!-- Cart Drawer -->
<div class="cart-drawer" id="cartDrawer">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Shopping Cart</h2>
        <button onclick="toggleCart()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
    </div>
    <div id="cartItems" style="overflow-y: auto; max-height: calc(100vh - 200px);">
        <!-- Cart items will be added here dynamically -->
    </div>
    <div style="position: absolute; bottom: 30px; left: 0; right: 0; padding: 20px; background: white; border-top: 1px solid #eee;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Total:</span>
            <span id="cartTotal">₱0.00</span>
        </div>
        <button onclick="checkout()" style="width: 100%; padding: 10px; background: #D5AD50; color: white; border: none; border-radius: 5px; cursor: pointer;">Checkout</button>
    </div>
</div>
<div class="cart-overlay" id="cartOverlay"></div>

<script>
    // Cart functionality
    let cart = [];
    const cartDrawer = document.getElementById('cartDrawer');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    function toggleCart() {
        const isOpen = cartDrawer.classList.contains('open');
        if (isOpen) {
            cartDrawer.classList.remove('open');
            cartOverlay.classList.remove('active');
            document.body.classList.remove('panel-open');
        } else {
            cartDrawer.classList.add('open');
            cartOverlay.classList.add('active');
            document.body.classList.add('panel-open');
        }
    }

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({...product, quantity: 1});
        }
        updateCart();
        toggleCart();
    }

    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCart();
    }

    function updateCart() {
        cartItems.innerHTML = '';
        let total = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const itemElement = document.createElement('div');
            itemElement.style.display = 'flex';
            itemElement.style.justifyContent = 'space-between';
            itemElement.style.alignItems = 'center';
            itemElement.style.padding = '10px';
            itemElement.style.borderBottom = '1px solid #eee';
            
            itemElement.innerHTML = `
                <div>
                    <h3 style="margin: 0;">${item.name}</h3>
                    <p style="margin: 5px 0;">₱${item.price.toFixed(2)} x ${item.quantity}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>₱${itemTotal.toFixed(2)}</span>
                    <button onclick="removeFromCart('${item.id}')" style="background: none; border: none; color: red; cursor: pointer;">×</button>
                </div>
            `;
            
            cartItems.appendChild(itemElement);
        });
        
        cartTotal.textContent = `₱${total.toFixed(2)}`;
    }

    function checkout() {
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }
        alert('Proceeding to checkout...');
    }

    // Close cart when clicking overlay
    cartOverlay.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleCart();
    });

    // Close cart when pressing escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && cartDrawer.classList.contains('open')) {
            toggleCart();
        }
    });

    // Initialize cart
    updateCart();
</script>

<script>
    // Notification functionality
    let notifications = [];
    const notificationDrawer = document.getElementById('notificationDrawer');
    const notificationOverlay = document.getElementById('notificationOverlay');
    const notificationItems = document.getElementById('notificationItems');
    const notificationCount = document.querySelector('.notification-count');

    function openNotifications() {
        notificationDrawer.classList.add('open');
        notificationOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeNotifications() {
        notificationDrawer.classList.remove('open');
        notificationOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function addNotification(notification) {
        notifications.unshift({
            ...notification,
            id: Date.now(),
            timestamp: new Date().toISOString(),
            read: false
        });
        updateNotifications();
    }

    function markAsRead(notificationId) {
        const notification = notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
            updateNotifications();
        }
    }

    function updateNotifications() {
        notificationItems.innerHTML = '';
        const unreadCount = notifications.filter(n => !n.read).length;
        
        notifications.forEach(notification => {
            const item = document.createElement('div');
            item.className = `notification-item ${notification.read ? 'read' : 'unread'}`;
            item.innerHTML = `
                <h3>${notification.title}</h3>
                <p>${notification.message}</p>
                <div class="timestamp">${new Date(notification.timestamp).toLocaleString()}</div>
            `;
            item.onclick = () => markAsRead(notification.id);
            notificationItems.appendChild(item);
        });
        
        notificationCount.textContent = unreadCount;
        localStorage.setItem('notifications', JSON.stringify(notifications));
    }

    // Initialize notifications from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const savedNotifications = localStorage.getItem('notifications');
        if (savedNotifications) {
            notifications = JSON.parse(savedNotifications);
        } else {
            // Add initial notifications if none exist
            notifications = [
                {
                    title: 'Welcome to iPawnshop',
                    message: 'Thank you for visiting our About page. Learn more about our team and services.',
                    timestamp: new Date().toISOString(),
                    read: false
                },
                {
                    title: 'Our Team',
                    message: 'Meet our dedicated team of professionals working to serve you better.',
                    timestamp: new Date(Date.now() - 3600000).toISOString(), // 1 hour ago
                    read: false
                },
                {
                    title: 'Contact Us',
                    message: 'Need help? Reach out to us through our contact details.',
                    timestamp: new Date(Date.now() - 7200000).toISOString(), // 2 hours ago
                    read: false
                }
            ];
        }
        updateNotifications();
    });

    // Function to add a new notification
    function addNewNotification(title, message) {
        addNotification({
            title: title,
            message: message
        });
    }

    // Example of how to add a notification (can be called from other parts of the code)
    // addNewNotification('New Feature', 'Check out our latest updates!');

    // Close notifications when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotifications();
        }
    });
</script>

</body>
</html>