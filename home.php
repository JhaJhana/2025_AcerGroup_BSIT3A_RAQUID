<?php
// Include the database connection
include 'db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: sss.php');
    exit();
}

// Get user profile picture
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'profile.jpg';

// Handle search functionality
$search_results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM homepage_content WHERE section = 'items' AND title LIKE '%$search_query%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: sss.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iPawnshop - Home</title>
    <link rel="stylesheet" href="home.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fdfdfd;
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0e2f1c; /* Updated color */
            padding: 10px 20px;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .search-bar {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            width: 200px;
        }

        .navbar .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            padding: 5px;
            font-size: 14px;
        }

        .navbar .search-bar button {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #4267B2;
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
            color: #f5cc59; /* Highlight on hover */
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

        /* Slider Section */
        .slider {
            position: relative;
            width: 100%;
            height: 500px; /* Keep the slider height */
            overflow: hidden;
            background-color: #fdfdfd; /* Background color for empty space */
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            width: 100%;
            flex-shrink: 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fdfdfd; /* Background color for empty space */
        }

        .slide img {
            width: 100%; /* Stretch the image to fill the width of the slide */
            height: 100%; /* Stretch the image to fill the height of the slide */
            object-fit: cover; /* Ensure the image fills the slide while maintaining aspect ratio */
            object-position: center; /* Center the image within the slide */
        }

        .slider .button {
            position: absolute;
            bottom: 20px; /* Position the button inside the slider */
            left: 50%;
            transform: translateX(-50%);
            background-color: #f5cc59;
            color: #0e2f1;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            z-index: 2; /* Ensure the button is above the image */
        }

        .slider .button:hover {
            background-color: #d4a437;
        }

        /* Remove any margin or padding between sections */
        body, html {
            margin: 0;
            padding: 0;
        }

        .slider, .slides, .slide {
            margin: 0;
            padding: 0;
        }

        /* Navigation Arrows */
        .slider .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000;
        }

        .slider .arrow.left {
            left: 10px;
        }

        .slider .arrow.right {
            right: 10px;
        }

        .slider .arrow:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Items Section */
        .items-section {
            padding: 4rem 2rem;
            text-align: center;
            background-color: #1d4b0b;
        }

        .items-section h2 {
            font-size: 2rem;
            color: white;
            margin-bottom: 2rem;
        }

        .items-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 200px;
            height: 200px;
            cursor: pointer;
            position: relative;
        }

        .item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item p {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(29, 75, 11, 0.9);
            color: white;
            margin: 0;
            padding: 10px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .estimator-form {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 1rem 2rem;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(40,167,69,0.06);
            transition: box-shadow 0.3s, transform 0.3s, background 0.3s;
        }
        .estimator-form:hover {
            box-shadow: 0 8px 24px rgba(40,167,69,0.18), 0 1.5px 8px rgba(40,167,69,0.10);
            background: #f6fff8;
            transform: translateY(-4px) scale(1.015);
            border: 1.5px solid #28a745;
        }
        .estimator-form .form-row {
            display: contents;
        }
        .estimator-form label {
            align-self: center;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.3rem;
            text-align: left;      /* <-- Make label text left-aligned */
            padding-right: 0.5rem;
        }
        .estimator-form input[type="text"],
        .estimator-form input[type="number"],
        .estimator-form select,
        .estimator-form textarea,
        .estimator-form input[type="file"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .estimator-form textarea {
            resize: vertical;
        }
        .estimator-form .form-row.buttons-row {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
            gap: 1.5rem;
        }
        .estimator-form .form-row.buttons-row a,
        .estimator-form .form-row.buttons-row button {
            min-width: 140px;
            max-width: 200px;
            text-align: center;
        }
        @media (max-width: 600px) {
            .estimator-form {
                grid-template-columns: 1fr;
            }
            .estimator-form label {
                text-align: left;
                padding-right: 0;
            }
            .estimator-form .form-row.buttons-row {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .estimator-form select:focus,
        .estimator-form input[type="text"]:focus,
        .estimator-form input[type="number"]:focus,
        .estimator-form textarea:focus {
            outline: 2px solid #d4a437;
            background-color: #fffde7;
            transition: background 0.2s, outline 0.2s;
        }
        .estimator-form input[type="file"]::-webkit-file-upload-button {
            background: #d4a437;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
        }
        .estimator-form input[type="file"]::file-selector-button {
            background: #d4a437;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .estimator-form .form-row > * {
            transition: box-shadow 0.2s, background 0.2s, border-color 0.2s;
        }

        .estimator-form .form-row:hover > * {
            box-shadow: 0 2px 12px rgba(40,167,69,0.10);
            background: #f6fff8;
            border-color: #28a745;
        }

        /* Make Back and Estimate Now buttons hoverable */
        .estimator-form .form-row.buttons-row a.button,
        .estimator-form .form-row.buttons-row button.button {
            transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
        }

        .estimator-form .form-row.buttons-row a.button:hover {
            background: #555;
            color: #fff;
            box-shadow: 0 4px 16px rgba(40,167,69,0.15);
            transform: translateY(-2px) scale(1.04);
            text-decoration: none;
        }

        .estimator-form .form-row.buttons-row button.button:hover {
            background: #218838;
            color: #fff;
            box-shadow: 0 4px 16px rgba(40,167,69,0.15);
            transform: translateY(-2px) scale(1.04);
        }

        .items-section#items-section {
            background-color: #1d4b0b !important;
        }
        .items-section#items-section h2,
        .items-section#items-section p {
            color: #fff;
        }

        /* Move navigation icons to the right, beside the profile menu */
        .navbar .nav-right {
            display: flex;
            align-items: center;
            gap: 30px;
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

    /* Add these styles in the existing style section */
    .faq-container {
        max-width: 800px;
        margin: 2rem auto;
    }

    .faq-item {
        background-color: white;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .faq-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .faq-question {
        padding: 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
        color: #1e460e;
        transition: all 0.3s ease;
        position: relative;
        background-color: white;
        text-align: center;
    }

    .faq-question:hover {
        background-color: #f8f8f8;
    }

    .faq-question.active {
        background-color: #f0f0f0;
    }

    .faq-answer {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
        padding: 0 20px;
        transform: translateY(-10px);
        text-align: center;
        line-height: 1.6;
        font-size: 15px;
        color: #333;
    }

    .faq-answer.active {
        max-height: 200px;
        opacity: 1;
        padding: 15px 20px 25px 20px;
        transform: translateY(0);
        border-top: 1px solid #eee;
    }

    .faq-question::after {
        content: '+';
        font-size: 24px;
        color: #1e460e;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: absolute;
        right: 20px;
    }

    .faq-question.active::after {
        content: '−';
        transform: rotate(180deg);
    }

    /* Add responsive padding for different screen sizes */
    @media (max-width: 768px) {
        .faq-answer {
            padding: 0 15px;
        }
        
        .faq-answer.active {
            padding: 15px 15px 25px 15px;
        }
    }

    /* Cart Drawer Styles */
    .cart-drawer {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: #0F3C2C;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: right 0.3s ease-in-out;
        padding: 20px;
        overflow-y: auto;
        color: white;
        visibility: hidden; /* Hide by default */
        opacity: 0; /* Hide by default */
    }

    .cart-drawer.open {
        right: 0;
        visibility: visible; /* Show when open */
        opacity: 1; /* Show when open */
    }

    .cart-drawer h2 {
        color: #D5AD50;
        margin: 0;
    }

    .cart-drawer button[onclick="closeCart()"] {
        background: none;
        border: none;
        font-size: 26px;
        cursor: pointer;
        color: #D5AD50;
        padding: 5px;
        transition: transform 0.2s ease;
    }

    .cart-drawer button[onclick="close()"] {
        background: none;
        border: none;
        font-size: 26px;
        cursor: pointer;
        color: #D5AD50;
        padding: 5px;
        transition: transform 0.2s ease;
    }

    .cart-drawer button[onclick="close()"] {
        background: none;
        border: none;
        font-size: 26px;
        cursor: pointer;
        color: #D5AD50;
        padding: 5px;
        transition: transform 0.2s ease;
    }

    .cart-drawer > div:last-child {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 15px 20px;
        background: #0F3C2C;
        border-top: 1px solid rgba(213, 173, 80, 0.2);
    }

    #cartItems {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 20px;
        margin-bottom: 100px;
    }

    #cartItems div {
        border-bottom: 1px solid rgba(213, 173, 80, 0.2);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 5px;
    }

    #cartItems h3 {
        color: #D5AD50;
        margin: 0;
        font-size: 14px;
    }

    #cartItems p {
        color: white;
        margin: 5px 0;
        font-size: 13px;
    }

    #cartItems button {
        background: none;
        border: none;
        color: #ff6b6b;
        cursor: pointer;
        font-size: 22px;
        padding: 5px;
        transition: transform 0.2s ease;
    }

    #cartItems button:hover {
        transform: scale(1.1);
    }

    .cart-drawer > div:last-child span {
        color: #D5AD50;
        font-weight: bold;
        font-size: 16px;
        display: block;
        margin-bottom: 15px;
    }

    .cart-drawer > div:last-child button {
        background: #D5AD50;
        color: #0F3C2C;
        font-weight: bold;
        border-radius: 8px;
        padding: 12px 20px;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .cart-drawer > div:last-child button:hover {
        background: #c49a3f;
        transform: translateY(-2px);
    }

    .cart-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .cart-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    body.panel-open {
        overflow: hidden;
    }

    /* Cart Icon Styles */
    .cart-container {
        position: relative;
        cursor: pointer;
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #D5AD50;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
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

    /* Update the notification slider styles */
    .notification-slider {
        position: fixed;
        top: 50%;
        right: 0;
        width: 300px;
        background: #0F3C2C;
        border-radius: 10px 0 0 10px;
        box-shadow: -2px 0 5px rgba(0,0,0,0.15);
        z-index: 1002;
        transform: translateX(0); /* Always visible */
        transition: transform 0.3s ease;
        overflow: hidden;
        visibility: visible;
        opacity: 1;
        margin-top: -150px; /* Center it vertically */
    }

    .notification-slider-content {
        padding: 15px;
        color: white;
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-slider-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(213, 173, 80, 0.2);
        position: sticky;
        top: 0;
        background: #0F3C2C;
        z-index: 1;
    }

    .notification-slider-item {
        padding: 10px;
        border-bottom: 1px solid rgba(213, 173, 80, 0.1);
        cursor: pointer;
        transition: background 0.2s ease;
        margin-bottom: 5px;
    }

    .notification-slider-item:hover {
        background: rgba(213, 173, 80, 0.1);
    }

    /* Add these notification styles */
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ff4444;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        display: none;
    }

    .notification-panel {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: #0F3C2C;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: transform 0.3s ease;
        color: white;
        display: flex;
        flex-direction: column;
    }

    .notification-panel.open {
        transform: translateX(-350px);
    }

    .notification-header {
        padding: 20px;
        border-bottom: 1px solid rgba(213, 173, 80, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-header h3 {
        margin: 0;
        color: #D5AD50;
    }

    .close-notification {
        background: none;
        border: none;
        color: #D5AD50;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
    }

    .notification-content {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
    }

    .notification-item {
        padding: 10px;
        border-bottom: 1px solid rgba(213, 173, 80, 0.2);
        margin-bottom: 10px;
        border-radius: 5px;
        background: rgba(213, 173, 80, 0.1);
    }

    .notification-item h4 {
        margin: 0 0 5px 0;
        color: #D5AD50;
    }

    .notification-item p {
        margin: 0;
        font-size: 14px;
    }

    .notification-item .time {
        font-size: 12px;
        color: #D5AD50;
        margin-top: 5px;
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
        transition: opacity 0.3s ease;
    }

    .notification-overlay.active {
        visibility: visible;
        opacity: 1;
    }
    </style>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script src="notifications.js" defer></script>
    <script>
        // Toggle profile dropdown visibility
        function toggleProfileMenu() {
            const profileMenu = document.querySelector('.profile-menu');
            profileMenu.classList.toggle('active');
        }

        // Close the dropdown if clicked outside
        document.addEventListener('click', function (event) {
            const profileMenu = document.querySelector('.profile-menu');
            if (!profileMenu.contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        });

        let currentSlide = 0;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        let animationID;
        const slides = document.querySelectorAll('.slide');

        function showSlide(index) {
            const slidesContainer = document.querySelector('.slides');
            const totalSlides = slidesContainer.children.length;
            currentSlide = (index + totalSlides) % totalSlides;
            slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function touchStart(index) {
            return function (event) {
                isDragging = true;
                startPos = getPositionX(event);
                animationID = requestAnimationFrame(animation);
                slides[index].classList.add('grabbing');
            };
        }

        function touchEnd() {
            isDragging = false;
            cancelAnimationFrame(animationID);
            const movedBy = currentTranslate - prevTranslate;

            if (movedBy < -100 && currentSlide < slides.length - 1) nextSlide();
            if (movedBy > 100 && currentSlide > 0) prevSlide();

            setPositionByIndex();
            slides.forEach(slide => slide.classList.remove('grabbing'));
        }

        function touchMove(event) {
            if (isDragging) {
                const currentPosition = getPositionX(event);
                currentTranslate = prevTranslate + currentPosition - startPos;
            }
        }

        function getPositionX(event) {
            return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
        }

        function animation() {
            const slidesContainer = document.querySelector('.slides');
            slidesContainer.style.transform = `translateX(${currentTranslate}px)`;
            if (isDragging) requestAnimationFrame(animation);
        }

        function setPositionByIndex() {
            currentTranslate = currentSlide * -window.innerWidth;
            prevTranslate = currentTranslate;
            const slidesContainer = document.querySelector('.slides');
            slidesContainer.style.transform = `translateX(${currentTranslate}px)`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const slidesContainer = document.querySelector('.slides');
            slidesContainer.addEventListener('mousedown', touchStart(0));
            slidesContainer.addEventListener('mouseup', touchEnd);
            slidesContainer.addEventListener('mousemove', touchMove);
            slidesContainer.addEventListener('mouseleave', touchEnd);

            slidesContainer.addEventListener('touchstart', touchStart(0));
            slidesContainer.addEventListener('touchend', touchEnd);
            slidesContainer.addEventListener('touchmove', touchMove);

            setInterval(nextSlide, 5000); // Auto-slide every 5 seconds
        });
        // Simple estimator logic
        const multipliers = {
            "18k": 1800,
            "22k": 2200,
            "24k": 2500
        };

        function updateEstimate() {
            const grams = parseFloat(document.getElementById('grams').value) || 0;
            const model = document.getElementById('model').value;
            let estimate = 0;
            if (grams > 0 && multipliers[model]) {
                estimate = grams * multipliers[model];
            }
            document.getElementById('estimateResult').textContent = "Estimated Value: ₱" + estimate.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        }

        document.getElementById('grams').addEventListener('input', updateEstimate);
        document.getElementById('model').addEventListener('change', updateEstimate);

        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    // Close all other answers
                    faqQuestions.forEach(otherQuestion => {
                        if (otherQuestion !== this) {
                            otherQuestion.classList.remove('active');
                            otherQuestion.nextElementSibling.classList.remove('active');
                        }
                    });
                    
                    // Toggle current question and answer
                    this.classList.toggle('active');
                    const answer = this.nextElementSibling;
                    answer.classList.toggle('active');
                });
            });
        });

        function toggleFAQ(element) {
            // Close all other answers with animation
            const allQuestions = document.querySelectorAll('.faq-question');
            allQuestions.forEach(question => {
                if (question !== element) {
                    if (question.classList.contains('active')) {
                        question.classList.remove('active');
                        const answer = question.nextElementSibling;
                        answer.style.maxHeight = '0';
                        answer.style.opacity = '0';
                        answer.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            answer.classList.remove('active');
                        }, 400); // Match this with CSS transition duration
                    }
                }
            });

            // Toggle current question and answer with animation
            element.classList.toggle('active');
            const answer = element.nextElementSibling;
            
            if (element.classList.contains('active')) {
                answer.classList.add('active');
                // Force reflow
                answer.offsetHeight;
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.style.opacity = '1';
                answer.style.transform = 'translateY(0)';
            } else {
                answer.style.maxHeight = '0';
                answer.style.opacity = '0';
                answer.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    answer.classList.remove('active');
                }, 400); // Match this with CSS transition duration
            }
        }

        // Add hover effect for FAQ items
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                });
            });
        });

        // Cart functionality
        let cart = [];
        const cartDrawer = document.getElementById('cartDrawer');
        const cartOverlay = document.getElementById('cartOverlay');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const cartCount = document.querySelector('.cart-count');

        // Initialize cart from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCart();
            }
        });

        function openCart() {
            const cartDrawer = document.getElementById('cartDrawer');
            const cartOverlay = document.getElementById('cartOverlay');
            
            // First make it visible
            cartDrawer.style.visibility = 'visible';
            cartDrawer.style.opacity = '1';
            
            // Then add the open class for the slide animation
            setTimeout(() => {
                cartDrawer.classList.add('open');
                cartOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }, 10);
        }

        function closeCart() {
            const cartDrawer = document.getElementById('cartDrawer');
            const cartOverlay = document.getElementById('cartOverlay');
            
            // First remove the open class for the slide animation
            cartDrawer.classList.remove('open');
            cartOverlay.classList.remove('active');
            document.body.style.overflow = '';
            
            // Then hide it after the animation
            setTimeout(() => {
                cartDrawer.style.visibility = 'hidden';
                cartDrawer.style.opacity = '0';
            }, 300); // Match this with the transition duration
        }

        function addToCart(product) {
            const existingItem = cart.find(item => item.id === product.id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({...product, quantity: 1});
            }
            updateCart();
            openCart();
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
                
                cartItems.innerHTML += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid rgba(213, 173, 80, 0.2);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <div>
                                <h3 style="margin: 0; color: #D5AD50;">${item.name}</h3>
                                <p style="margin: 5px 0; color: white;">₱${item.price.toFixed(2)} x ${item.quantity}</p>
                            </div>
                        </div>
                        <button onclick="removeFromCart('${item.id}')" style="background: none; border: none; color: #ff6b6b; cursor: pointer; font-size: 22px;">×</button>
                    </div>
                `;
            });
            
            cartTotal.textContent = `₱${total.toFixed(2)}`;
            cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            alert('Proceeding to checkout...');
        }

        // Close cart when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCart();
            }
        });

        // Notification functionality
        let notifications = [];
        const notificationDrawer = document.getElementById('notificationDrawer');
        const notificationOverlay = document.getElementById('notificationOverlay');
        const notificationItems = document.getElementById('notificationItems');
        const notificationCount = document.querySelector('.notification-count');

        function openNotifications() {
            // First make it visible
            notificationDrawer.style.visibility = 'visible';
            notificationDrawer.style.opacity = '1';
            
            // Then add the open class for the slide animation
            setTimeout(() => {
                notificationDrawer.classList.add('open');
                notificationOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }, 10);
        }

        function closeNotifications() {
            // First remove the open class for the slide animation
            notificationDrawer.classList.remove('open');
            notificationOverlay.classList.remove('active');
            document.body.style.overflow = '';
            
            // Then hide it after the animation
            setTimeout(() => {
                notificationDrawer.style.visibility = 'hidden';
                notificationDrawer.style.opacity = '0';
            }, 300);
        }

        function addNotification(notification) {
            notifications.unshift({
                ...notification,
                id: Date.now(),
                timestamp: new Date().toISOString(),
                read: false
            });
            updateNotifications();
            showNotificationSlider(notification); // Show the slider for new notifications
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
                updateNotifications();
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

        function showNotificationSlider(notification) {
            let slider = document.querySelector('.notification-slider');
            
            if (!slider) {
                slider = document.createElement('div');
                slider.className = 'notification-slider';
                document.body.appendChild(slider);
            }
            
            const content = document.createElement('div');
            content.className = 'notification-slider-item';
            content.innerHTML = `
                <h4>${notification.title}</h4>
                <p>${notification.message}</p>
                <div class="timestamp">${new Date().toLocaleString()}</div>
            `;
            
            const sliderContent = slider.querySelector('.notification-slider-content');
            if (sliderContent) {
                sliderContent.insertBefore(content, sliderContent.firstChild);
            } else {
                slider.innerHTML = `
                    <div class="notification-slider-content">
                        <div class="notification-slider-header">
                            <h3>Notifications</h3>
                            <button class="notification-slider-close" onclick="clearNotifications()">&times;</button>
                        </div>
                        ${content.outerHTML}
                    </div>
                `;
            }
        }

        function clearNotifications() {
            const slider = document.querySelector('.notification-slider');
            if (slider) {
                const content = slider.querySelector('.notification-slider-content');
                if (content) {
                    content.innerHTML = `
                        <div class="notification-slider-header">
                            <h3>Notifications</h3>
                            <button class="notification-slider-close" onclick="clearNotifications()">&times;</button>
                        </div>
                    `;
                }
            }
        }

        // Add click event listener to the notification bell
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBell = document.querySelector('.notification-container button');
            if (notificationBell) {
                notificationBell.addEventListener('click', function(e) {
                    e.preventDefault();
                    openNotifications();
                });
            }
        });

        // Add this test function to demonstrate the slider
        function testNotificationSlider() {
            const testNotification = {
                title: 'Test Notification',
                message: 'This is a test notification to demonstrate the slider visibility.'
            };
            showNotificationSlider(testNotification);
        }

        // Add this to your DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', function() {
            // ... existing code ...
            
            // Show initial notification
            showNotificationSlider({
                title: 'Welcome',
                message: 'Welcome to iPawnshop!'
            });
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
                <span class="cart-count">0</span>
            </div>

            <!-- Notification Bell -->
            <div style="position: relative;">
                <button id="notificationBtn" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 0;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge">0</span>
                </button>
                <div class="notification-panel" id="notificationPanel">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        <button class="close-notification" onclick="window.notificationSystem.toggleNotification()">&times;</button>
                    </div>
                    <div class="notification-content" id="notificationContent">
                        <!-- Notifications will be added here dynamically -->
                    </div>
                </div>
            </div>

            <!-- SANGLA NOW Button -->
            <form action="sangla.php" method="GET" style="display: inline;">
                <button type="submit" style="background-color: #f5cc59; color: #0e2f1c; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    SANGLA NOW
                </button>
            </form>

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

    <!-- First Section: Slider -->
    <div class="slider">
        <div class="slides">
            <div class="slide">
                <img src="images/slider.png" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="images/slider2.png" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="images/slider3.png" alt="Slide 3">
            </div>
        </div>
        <button class="button" onclick="window.location.href='sangla.php'">SANGLA NOW</button>
        <button class="arrow left" onclick="prevSlide()">&#10094;</button>
        <button class="arrow right" onclick="nextSlide()">&#10095;</button>
    </div>

    <!-- Sangla Estimator-->
    <section style="background-color: #f3cf7a; padding: 7rem 2rem;">
        <div style="display: flex; justify-content: center; align-items: normal; gap: 40px; flex-wrap: wrap;">
            <!-- Left Text -->
            <div style="text-align: right;">
                <h2 style="color: #1e460e; font-size: 40px; font-weight: bold;">sa iPawnShop</h2>
                <p style="font-style: italic; color: #8a5b1d; font-size: 28px;">
                    Mabilis, Madali,<br />
                    Maaasahan
                </p>
            </div>

            <!-- Center Estimator -->
            <div onclick="window.location.href='estimator.php'" style="
                position: relative;
                width: 350px;
                height: 350px;
                border-radius: 25px;
                overflow: hidden;
                background-color: #fff6d5;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        cursor: pointer;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';" 
               onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)';">
                <!-- Background Image -->
                <img src="images/sanglaest.jpg" alt="Jewelry" style="
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    opacity: 1;
                    transition: opacity 0.3s ease;
                ">

                <!-- Text Layer -->
                <div style="
                    position: relative;
                    z-index: 1.5;
                    color: #1e460e;
                    padding: 2rem;
                    text-align: left;
                ">
                    <h3 style="margin: 0; font-size: 40px;">
                        <strong>SANGLA<br>ESTIMATOR</strong>
                    </h3>
                    <p style="font-size: 20px;">Estimate your jewelry's appraisal here →</p>
                </div>
            </div>

            <!-- Right Buttons -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <!-- TUBO -->
                <div onclick="window.location.href='tubo.php'" style="
                    background-color: #bd8f13;
                    color: #1e460e;
                    padding: 2rem;
                    border-radius: 15px;
                    width: 120px;
                    text-align: left;
                    cursor: pointer;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';" 
                   onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                    <div style="font-size: 28px; font-weight: 800;">TUBO</div>
                    <small style="font-size: 18px;">Pay your monthly tubo here →</small>
                </div>

                <!-- TUBOS -->
                <div onclick="window.location.href='tubos.php'" style="
                    background-color: #bd8f13;
                    color: #1e460e;
                    padding: 2rem;
                    border-radius: 15px;
                    width: 120px;
                    text-align: left;
                    cursor: pointer;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';" 
                   onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                    <div style="font-size: 28px; font-weight: 800;">TUBOS</div>
                    <small style="font-size: 18px;">Pay your redemption amount here →</small>
                </div>
            </div>
        </div>
    </section>

    <!-- Second Section: Items We Accept -->
    <section class="items-section" id="items-section" style="background-color: #1d4b0b; padding: 4rem 2rem; text-align: center;">
        <h2 style="color: white; font-size: 2rem; margin-bottom: 2rem;">Items we Accept</h2>
        <div class="items-container">
            <div class="item" onclick="addToCart({
                id: 'necklace1',
                name: 'Gold Necklace',
                price: 5000,
                image: 'images/necklace.jpg'
            })">
                <img src="images/necklace.jpg" alt="Necklace">
                <p>Necklace</p>
            </div>
            <div class="item" onclick="addToCart({
                id: 'earrings1',
                name: 'Diamond Earrings',
                price: 3000,
                image: 'images/earrings.jpg'
            })">
                <img src="images/earrings.jpg" alt="Earrings">
                <p>Earrings</p>
            </div>
            <div class="item" onclick="addToCart({
                id: 'ring1',
                name: 'Gold Ring',
                price: 2000,
                image: 'images/ring.jpg'
            })">
                <img src="images/ring.jpg" alt="Ring">
                <p>Ring</p>
            </div>
            <div class="item" onclick="addToCart({
                id: 'watch1',
                name: 'Luxury Watch',
                price: 8000,
                image: 'images/watch.jpg'
            })">
                <img src="images/watch.jpg" alt="Watch">
                <p>Watch</p>
            </div>
            <div class="item" onclick="addToCart({
                id: 'bracelet1',
                name: 'Gold Bracelet',
                price: 4000,
                image: 'images/bracelet.jpg'
            })">
                <img src="images/bracelet.jpg" alt="Bracelet">
                <p>Bracelet</p>
            </div>
        </div>
    </section>

    <!-- Tutorial Section -->
    <section style="padding: 4rem 2rem; background-color: #d6af58; text-align: center;">
        <h2 style="color: white;">How to Use iPawn</h2>
        <p style="color: white;">Here is a 3-step guide and learn how to use the platform.</p>

        <div style="display: flex; justify-content: center; gap: 30px; margin-top: 2rem; flex-wrap: wrap;">
            <div style="position: relative; width: 300px; height: 250px; background-color: #bd8f13; border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 30px 20px;">
                <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); background-color: #1e460e; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: bold;">1</div>
                <div style="color: white; font-size: 18px; text-align: center; text-align-last: center; line-height: 1.5;">
                    <strong>Estimate Your Jewelry</strong><br>
                    Use our Sangla Estimator on the landing page to get an instant estimate of your jewelry's value.
                    Once you're ready, click "Sangla Now" to proceed.
                </div>
            </div>

            <div style="position: relative; width: 300px; height: 250px; background-color: #bd8f13; border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 30px 20px;">
                <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); background-color: #1e460e; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: bold;">2</div>
                <div style="color: white; font-size: 18px; text-align: center; text-align-last: center; line-height: 1.5;">
                    <strong>Submit Appraisal Request</strong><br>
                    Log in or create an account to continue.
                    Fill out the online appraisal form and upload photos of your jewelry.
                    After submitting, just wait for a notification with your appraisal result, interest, and loan offer.
                </div>
            </div>

            <div style="position: relative; width: 300px; height: 250px; background-color: #bd8f13; border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 30px 20px;">
                <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); background-color: #1e460e; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: bold;">3</div>
                <div style="color: white; font-size: 16px; text-align: center; text-align-last: center; line-height: 1.5;">
                    <strong>Accept & Visit the Branch</strong><br>
                    Review the offer, then click Accept if you agree to the terms.
                    A message will guide you to visit the nearest iPawnShop branch to verify your jewelry and receive your loan.
                    Once completed, track your transaction status anytime in your Sangla Dashboard.
                </div>
            </div>
        </div>
    </section>

    <!-- View Our Marketplace Section -->
    <section style="padding: 4rem 2rem; background-color: #fdfdfd; text-align: center;">
        <h2 style="color: #0e2f1c;">View Our Marketplace</h2>
        <p style="color: #333;">Discover quality remata items at the best prices.</p>
        <button onclick="window.location.href='market.php'" style="margin-top: 1rem; background-color: #f5cc59; color: #0e2f1c; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Visit Marketplace</button>
    </section>

    <!-- FAQs Section -->
    <section style="padding: 4rem 2rem; background-color: #d6af58; text-align: center;">
        <h2 style="color: white; margin-bottom: 2rem;">Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">How do I pawn an item?</div>
                <div class="faq-answer">
                    Click on "Sangla Now" and follow the steps to submit your item details. Our process is simple and straightforward.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">How long does the appraisal take?</div>
                <div class="faq-answer">
                    Appraisal usually takes less than 24 hours. We work quickly to provide you with a fair assessment of your item's value.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">What items can I pawn?</div>
                <div class="faq-answer">
                    We accept necklace, earrings, rings, watch, and bracelet. All items are carefully evaluated for their value and authenticity.
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Logo Section -->
            <div class="footer-section" style="flex: 1; display: flex; justify-content: flex-start;">
                <img src="images/logo1.jpg" alt="Footer Logo" style="width: 150px; height: 150px;">
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
            <h2 style="margin: 0; color: #D5AD50;">Shopping Cart</h2>
            <button onclick="closeCart()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #D5AD50;">&times;</button>
        </div>
        <div id="cartItems"></div>
        <div style="position: absolute; bottom: 30px; left: 0; right: 0; padding: 20px; background: #0F3C2C; border-top: 1px solid rgba(213, 173, 80, 0.2);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #D5AD50;">Total:</span>
                <span id="cartTotal" style="color: #D5AD50;">₱0.00</span>
            </div>
            <button onclick="checkout()" style="width: 100%; padding: 10px; background: #D5AD50; color: #0F3C2C; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Checkout</button>
        </div>
    </div>
    <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>

    <!-- Notification Drawer -->
    <div class="notification-drawer" id="notificationDrawer">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #D5AD50;">Notifications</h2>
            <button type="button" onclick="closeNotifications()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #D5AD50;">&times;</button>
        </div>
        <div id="notificationItems" style="flex: 1; overflow-y: auto; padding-right: 10px;">
            <!-- Notifications will be added here dynamically -->
        </div>
    </div>
    <div class="notification-overlay" id="notificationOverlay"></div>
</body>
</html>

    <!-- Search Results Section -->
    <?php if (!empty($search_results)): ?>
        <div class="search-results">
            <h2>Search Results</h2>
            <div class="items-container">
                <?php foreach ($search_results as $item): ?>
                    <div class="item">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>" onclick="window.location.href='<?php echo $item['link_url']; ?>'">
                        <p><?php echo $item['title']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>