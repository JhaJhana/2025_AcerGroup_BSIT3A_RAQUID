<?php
// Include the database connection
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
    <title>Branches</title>

    <!-- AOS & Feather Icons -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7d16c;
            color: white;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0e2f1c;
            padding: 10px 20px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            width: 200px;
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            padding: 5px;
            font-size: 14px;
        }

        .search-bar button {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #4267B2;
        }

        .nav-icons {
            display: flex;
            gap: 20px;
        }

        .nav-icons a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }

        .nav-icons a:hover {
            color: #f5b60f;
        }

        .profile-menu {
            position: relative;
        }

        .profile-icon {
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

        .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dropdown {
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

        .dropdown a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }

        .dropdown a:hover {
            background-color: #f5f5f5;
        }

        .profile-menu.active .dropdown {
            display: block;
        }

        /* Branch Section */
        .branch-section {
            font-family: Arial, sans-serif;
            font-weight: normal;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4rem 2rem;
            min-height: 100vh;
        }

        .branch-section h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #1e4b0c;
            font-weight: 700;
    
        }

        .branch-container {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 20px;
    flex-wrap: nowrap;
}



.branch {
    flex: 0 0 auto; /* ensures each branch stays fixed width */
    width: 300px;
    height: 200px;
    position: relative;
    background-color: rgba(245, 182, 15, 0.8);
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}

.branch .hover-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    padding: 8px 16px;
    font-size: 1rem;
    font-weight: bold;
    opacity: 0;
    transition: opacity 0.3s ease;
    text-align: center;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border-radius: 8px;
    text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}



.branch:hover .hover-text {
    opacity: 0;
    opacity: 1;
}

        .branch:hover {
            transform: scale(1.06);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        }

        .branch img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: filter 0.3s ease, transform 0.3s ease;
    filter: none; /* Start clear */
}

.branch:hover img {
    filter: blur(3px); /* Becomes blurry on hover */
    transform: scale(1.05);
}


.branch-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 8px 16px;
    font-size: 1.8rem;
    color: white;
    font-weight: bold;
    text-shadow: 0 0 5px rgba(0, 0, 0, 0.5); /* slight glow for visibility */
    backdrop-filter: blur(4px); /* background blur effect */
    -webkit-backdrop-filter: blur(4px); /* Safari support */
    border-radius: 8px;
    transition: opacity 0.3s ease;
    opacity: 1;
}



        .branch:hover .branch-label {
            opacity: 0;
            background: rgba(0, 0, 0, 0.7);
            transform: translateX(-50%) translateY(-5px);
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
            <div class="cart-container" style="position: relative;">
                <button onclick="openCart()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <span class="cart-count" style="position: absolute; top: -8px; right: -8px; background: #D5AD50; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;">0</span>
            </div>

            <!-- Notification Bell -->
            <div class="notification-container" style="position: relative;">
                <button onclick="openNotifications()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
                    <i class="fas fa-bell"></i>
                </button>
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


    <!-- Branch Section -->
<section class="branch-section">
    <h1 data-aos="fade-down">Our Branches</h1>

    <div class="branch-container">
        <!-- Polangui -->
        <div class="branch" data-aos="zoom-in" onclick="location.href='ligao.php'">
            <img src="images/polangui.jpg" alt="Polangui Branch">
            <div class="branch-label">Polangui</div>
            <div class="hover-text">Pluto Street, Centro Occidental, Polangui, Albay</div>
        </div>

        <!-- Libon -->
        <div class="branch" data-aos="zoom-in" data-aos-delay="100" onclick="location.href='tabaco.php'">
            <img src="images/libon.jpg" alt="Libon Branch">
            <div class="branch-label">Libon</div>
            <div class="hover-text">Zone 7 Del Rosario, Libon, Albay</div>
        </div>

        <!-- Ligao City -->
        <div class="branch" data-aos="zoom-in" data-aos-delay="200" onclick="location.href='legazpi.php'">
            <img src="images/ligao.jpg" alt="Ligao City Branch">
            <div class="branch-label">Ligao City</div>
            <div class="hover-text">Zone 1 Tuburan, Ligao City, Albay</div>
        </div>
    </div>
</section>

<!-- Cart Drawer -->
<div class="cart-drawer" id="cartDrawer">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Shopping Cart</h2>
        <button onclick="closeCart()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
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
        }
        // Add a test notification
        addNotification({
            title: 'Welcome',
            message: 'Welcome to iPawnshop!'
        });
    });

    // Close notifications when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotifications();
        }
    });

    // Cart functionality
    let cart = [];
    const cartDrawer = document.getElementById('cartDrawer');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    function openCart() {
        cartDrawer.classList.add('open');
        cartOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCart() {
        cartDrawer.classList.remove('open');
        cartOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({...product, quantity: 1});
        }
        updateCart();
        closeCart();
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
        closeCart();
    });

    // Close cart when pressing escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && cartDrawer.classList.contains('open')) {
            closeCart();
        }
    });

    // Initialize cart
    updateCart();
</script>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
        feather.replace();
    </script>

</body>
</html>
