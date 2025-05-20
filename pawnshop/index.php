<?php
// index.php - Main product listing page
// Include config file
require_once "config.php";

// Start session
session_start();

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables
$search = "";
$category = "all";
$material = "";
$sort = "price-low-high";
$page = 1;
$items_per_page = 6;

// Process search query
if(isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

// Process category filter
if(isset($_GET["category"])) {
    $category = $_GET["category"];
}

// Process material filter
if(isset($_GET["material"])) {
    $material = $_GET["material"];
}

// Process sort order
if(isset($_GET["sort"])) {
    $sort = $_GET["sort"];
}

// Process pagination
if(isset($_GET["page"])) {
    $page = (int)$_GET["page"];
    if($page < 1) $page = 1;
}

// Calculate offset for SQL LIMIT clause
$offset = ($page - 1) * $items_per_page;

// Prepare base SQL query
$sql = "SELECT * FROM products WHERE 1=1";

// Add search condition if provided
if(!empty($search)) {
    $search = $mysqli->real_escape_string($search);
    $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

// Add category filter if not 'all'
if($category != "all") {
    $category = $mysqli->real_escape_string($category);
    $sql .= " AND category = '$category'";
}

// Add material filter if provided
if(!empty($material)) {
    $material = $mysqli->real_escape_string($material);
    $sql .= " AND material = '$material'";
}

// Add sorting
switch($sort) {
    case "price-low-high":
        $sql .= " ORDER BY price ASC";
        break;
    case "price-high-low":
        $sql .= " ORDER BY price DESC";
        break;
    case "newest":
        $sql .= " ORDER BY created_at DESC";
        break;
    case "popular":
        $sql .= " ORDER BY popularity DESC";
        break;
    default:
        $sql .= " ORDER BY price ASC";
}

// Add pagination limit
$sql .= " LIMIT $offset, $items_per_page";

// Execute query
$result = $mysqli->query($sql);

// Get total products count for pagination
$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
if(!empty($search)) {
    $count_sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}
if($category != "all") {
    $count_sql .= " AND category = '$category'";
}
if(!empty($material)) {
    $count_sql .= " AND material = '$material'";
}

$count_result = $mysqli->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry Store</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="search-container">
                <form action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search" class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="branch.php">Branch</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
            <div class="nav-right">
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    // Show cart count if there are items
                    if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
                        echo '<span class="cart-counter">' . $_SESSION['cart_count'] . '</span>';
                    }
                    ?>
                </a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Filter Sidebar -->
            <aside class="sidebar">
                <div class="filter-section">
                    <h3>FILTER</h3>
                    <form action="index.php" method="GET" id="filter-form">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        
                        <div class="filter-group">
                            <h4>Category:</h4>
                            <div class="filter-option">
                                <input type="radio" id="all" name="category" value="all" <?php if($category == "all") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="all">ALL</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="necklace" name="category" value="necklace" <?php if($category == "necklace") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="necklace">Necklace</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="ring" name="category" value="ring" <?php if($category == "ring") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="ring">Ring</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="earrings" name="category" value="earrings" <?php if($category == "earrings") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="earrings">Earrings</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="bracelet" name="category" value="bracelet" <?php if($category == "bracelet") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="bracelet">Bracelet</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="wristwatch" name="category" value="wristwatch" <?php if($category == "wristwatch") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="wristwatch">Wristwatch</label>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <div class="filter-option">
                                <input type="radio" id="18k-gold" name="material" value="18k gold" <?php if($material == "18k gold") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="18k-gold">18k Gold</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="22k-gold" name="material" value="22k gold" <?php if($material == "22k gold") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="22k-gold">22k Gold</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="24k-gold" name="material" value="24k gold" <?php if($material == "24k gold") echo "checked"; ?> onchange="this.form.submit()">
                                <label for="24k-gold">24k Gold</label>
                            </div>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Product Gallery -->
            <section class="product-gallery">
                <div class="gallery-header">
                    <div class="search-container secondary">
                        <form action="index.php" method="GET">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                            <input type="hidden" name="material" value="<?php echo htmlspecialchars($material); ?>">
                            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                            <input type="text" name="search" placeholder="Search" class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    <div class="sort-container">
                        <span>SORT BY:</span>
                        <form action="index.php" method="GET" id="sort-form">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                            <input type="hidden" name="material" value="<?php echo htmlspecialchars($material); ?>">
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="price-low-high" <?php if($sort == "price-low-high") echo "selected"; ?>>Price Low to High</option>
                                <option value="price-high-low" <?php if($sort == "price-high-low") echo "selected"; ?>>Price High to Low</option>
                                <option value="newest" <?php if($sort == "newest") echo "selected"; ?>>Newest</option>
                                <option value="popular" <?php if($sort == "popular") echo "selected"; ?>>Popular</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="products-container">
                    <?php
                    // Check if there are products
                    if($result && $result->num_rows > 0) {
                        // Output product cards
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="product-card">';
                            echo '<div class="product-image">';
                            echo '<img src="images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                            echo '</div>';
                            echo '<div class="product-info">';
                            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                            echo '<p>' . htmlspecialchars($row['weight']) . '</p>';
                            echo '<p class="price">₱' . number_format($row['price'], 0) . '</p>';
                            echo '</div>';
                            echo '<div class="product-actions">';
                            
                            // Check if product is in wishlist
                            $wishlist_class = "far"; // Regular heart icon by default
                            $wishlist_style = "";
                            if(isset($_SESSION['wishlist']) && in_array($row['id'], $_SESSION['wishlist'])) {
                                $wishlist_class = "fas"; // Solid heart icon for wishlist items
                                $wishlist_style = "style=\"color: #e76f51;\"";
                            }
                            
                            echo '<button class="wishlist-btn" data-id="' . $row['id'] . '" ' . $wishlist_style . '>';
                            echo '<i class="' . $wishlist_class . ' fa-heart"></i>';
                            echo '</button>';
                            echo '<button class="add-to-cart-btn" data-id="' . $row['id'] . '">Add to Cart</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-products">No products found matching your criteria.</div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <a href="?page=<?php echo ($page > 1) ? $page - 1 : 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&material=<?php echo urlencode($material); ?>&sort=<?php echo urlencode($sort); ?>" class="prev-page">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <span class="page-indicator"><?php echo $page; ?>/<?php echo $total_pages; ?></span>
                    <a href="?page=<?php echo ($page < $total_pages) ? $page + 1 : $total_pages; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&material=<?php echo urlencode($material); ?>&sort=<?php echo urlencode($sort); ?>" class="next-page">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>