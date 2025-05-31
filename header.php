<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection if needed
if (!isset($conn)) {
    include 'db.php';
}

// Get user profile picture
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'profile.jpg';
?>
<!-- Navbar -->
<div class="navbar">
    <!-- Search Bar -->
    <form class="search-bar" method="GET" action="">
        <input type="text" name="search" placeholder="Search..." value="">
        <button type="submit">🔍</button>
    </form>

    <!-- Navigation Icons -->
    <div class="nav-icons">
        <a href="home.php" data-tooltip="Home">🏠</a>
        <a href="javascript:void(0)" onclick="toggleCart()" data-tooltip="Cart">🛒</a>
        <a href="branch.php" data-tooltip="Branches">📍</a>
        <a href="about.php" data-tooltip="About">ℹ️</a>
    </div>

    <!-- Profile Menu -->
    <div class="profile-menu">
        <div class="profile-icon" onclick="toggleProfileMenu()">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
        </div>
        <div class="dropdown">
            <a href="profile.php">Profile</a>
            <a href="sangla.php">Sangla</a>
            <a href="tubo.php">Tubo</a>
            <a href="tubos.php">Tubos</a>
            <a href="sss.php">Logout</a>
        </div>
    </div>
</div>

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