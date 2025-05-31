<?php
// filepath: c:\xampp\htdocs\Prompting\landing.php

// Include the database connection if needed
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
      font-family: 'Arial', sans-serif;
      background-color: var(--background-light);
      color: #333;
      margin: 0;
      padding: 0;
    }

    .navbar {
      display: flex;
      justify-content: flex-end;
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

    .login-button {
      background-color: var(--accent-yellow);
      color: black;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: bold;
      text-decoration: none;
      margin-left: auto;
    }

    .login-button:hover {
      background-color: #e6c200;
    }

    .about-section {
      padding: 6rem 2rem;
      text-align: center;
      background-color: #2f7910;
      color: white;
    }

    .creators-section {
      padding: 6rem 2rem;
      text-align: center;
      background-color: #497e33;
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

      .creators-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <a href="login.php" class="login-button">Log In</a>
</div>

<!-- About Us Section -->
<section class="about-section">
  <div style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; max-width: 1200px; margin: 0 auto;">
    <div style="flex: 1; text-align: left;">
      <h1 class="text-5xl font-extrabold mb-6">About Us</h1>
      <p class="text-lg leading-relaxed">
       Welcome to iPawnshop, your modern and secure digital partner in pawn transactions. We are committed to transforming traditional pawnshop operations into a seamless, transparent, and customer-friendly experience. Our system empowers both customers and staff with real-time tracking, efficient item management, secure loan processing, and easy-to-use interfaces.At iPawnshop, we value trust, security, and innovation. Whether you're pawning, redeeming, or checking transaction history, we're here to make your journey simple and secure. Let's make smart pawning accessible to everyone—digitally.
      </p>
    </div>
    <div style="flex: 1; text-align: center;">
      <img src="images/footer.png" alt="iPawnshop Logo" style="max-width: 100%; height: auto; margin-left: 125px;">
    </div>
  </div>
</section>

<!-- Our Values and History -->
<section style="background-color: var(--accent-yellow); padding: 4rem 2rem; color: #333;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: row; gap: 4rem; flex-wrap: wrap;">
    <div style="flex: 1;">
      <h2 class="text-4xl font-semibold mb-4">Our History</h2>
      <p class="text-lg leading-relaxed max-w-xl">
                Founded in the heart of our community, iPawnshop began as a small family-owned venture dedicated to serving local needs. 
                Over the years, we've evolved from a traditional brick-and-mortar pawnshop into a cutting-edge digital platform. 
                While technology has changed how we operate, our commitment to people remains unchanged. 
                We're proud of our heritage and excited for the future.
      </p>
    </div>
    <div style="flex: 1;">
      <h2 class="text-4xl font-semibold mb-4">Our Values</h2>
      <p class="text-lg leading-relaxed max-w-xl ml-auto">
                At iPawnshop, we stand on the pillars of integrity, customer-first service, and technological innovation. 
                We believe in building lasting relationships founded on trust, transparency, and respect. Our goal is to empower every customer 
                through responsible financial solutions that are accessible, secure, and modern.
      </p>
    </div>
  </div>
</section>

<!-- Contact and Social Media -->
<section class="contact-social-section" style="background-color: var(--background-light); padding: 4rem 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 4rem; max-width: 1200px; margin: 0 auto; flex-wrap: wrap;">
    <div style="flex: 1;">
      <h2 class="text-3xl font-semibold mb-4">Contact Details</h2>
      <p><strong>Phone:</strong> 0907-536-5447</p>
      <p><strong>Email:</strong> support@ipawnshop.com</p>
      <p><strong>Address:</strong> 123 Pawnshop Lane, Philippines</p>
      <p><strong>Working Hours:</strong> Mon-Fri, 9:00 AM - 6:00 PM</p>
    </div>
    <div style="flex: 1;">
      <h2 class="text-3xl font-semibold mb-4">Follow Us on Social Media</h2>
      <p>Stay connected with us:</p>
      <ul style="list-style: none; padding: 0;">
        <li><a href="https://facebook.com" target="_blank" style="color: #2f7910;">Facebook</a></li>
        <li><a href="https://twitter.com" target="_blank" style="color: #2f7910;">Twitter</a></li>
        <li><a href="https://instagram.com" target="_blank" style="color: #2f7910;">Instagram</a></li>
      </ul>
    </div>
  </div>
</section>

<!-- Creators Section -->
<section class="creators-section">
  <h2 class="text-4xl font-semibold">Meet the Creators</h2>
  <div class="creators-grid">
    <div class="creator-container">
      <img src="images/Bea.jpg" alt="Bea">
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
                <img src="images/jha.jpg" alt="Creator 6">
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

</body>
</html> 