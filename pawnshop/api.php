<?php
// api.php - API endpoints for AJAX functionality

// Include config file
require_once "config.php";

// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // Return error if not logged in
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the requested action
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Handle different API actions
switch($action) {
    case 'add_to_cart':
        addToCart();
        break;
    case 'remove_from_cart':
        removeFromCart();
        break;
    case 'update_cart_quantity':
        updateCartQuantity();
        break;
    case 'toggle_wishlist':
        toggleWishlist();
        break;
    default:
        // Unknown action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}

// Function to add a product to cart
function addToCart() {
    global $mysqli;
    
    // Get product ID
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if($product_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    // Validate that product exists
    $sql = "SELECT id, name, price FROM products WHERE id = ?";
    
    if($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        
        if($stmt->execute()) {
            $stmt->store_result();
            
            if($stmt->num_rows == 1) {
                // Declare variables before binding
                $id = $name = $price = null;
                if ($stmt->bind_result($id, $name, $price)) {
                    $stmt->fetch();
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to bind result']);
                    return;
                }
                
                // Initialize cart if it doesn't exist
                if(!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = array();
                    $_SESSION['cart_count'] = 0;
                }
                
                // Check if product is already in cart
                if(isset($_SESSION['cart'][$product_id])) {
                    // Increment quantity if already in cart
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    // Add new product to cart
                    $_SESSION['cart'][$product_id] = array(
                        'id' => $id,
                        'name' => $name,
                        'price' => $price,
                        'quantity' => $quantity
                    );
                    
                    // Increment cart count
                    $_SESSION['cart_count']++;
                }
                
                // Return success response
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Product added to cart',
                    'cart_count' => $_SESSION['cart_count']
                ]);
            } else {
                // Product not found
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
        } else {
            // Execution failed
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        
        $stmt->close();
    } else {
        // Preparation failed
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// Function to remove a product from cart
function removeFromCart() {
    // Get product ID
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if($product_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    // Check if product is in cart
    if(isset($_SESSION['cart'][$product_id])) {
        // Remove product from cart
        unset($_SESSION['cart'][$product_id]);
        
        // Decrement cart count
        $_SESSION['cart_count']--;
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Product removed from cart',
            'cart_count' => $_SESSION['cart_count']
        ]);
    } else {
        // Product not in cart
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product not in cart']);
    }
}

// Function to update cart item quantity
function updateCartQuantity() {
    // Get product ID and new quantity
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    if($product_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    if($quantity <= 0) {
        // If quantity is 0 or negative, remove from cart
        removeFromCart();
        return;
    }
    
    // Check if product is in cart
    if(isset($_SESSION['cart'][$product_id])) {
        // Update quantity
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        
        // Calculate new total
        $total = $_SESSION['cart'][$product_id]['price'] * $quantity;
        
        // Return success response with updated total
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Quantity updated',
            'total' => $total,
            'formatted_total' => '₱' . number_format($total, 0)
        ]);
    } else {
        // Product not in cart
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product not in cart']);
    }
}

// Function to toggle wishlist status for a product
function toggleWishlist() {
    // Get product ID
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if($product_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    // Initialize wishlist if it doesn't exist
    if(!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array();
    }
    
    // Check if product is already in wishlist
    $index = array_search($product_id, $_SESSION['wishlist']);
    
    if($index !== false) {
        // Remove from wishlist if already in it
        unset($_SESSION['wishlist'][$index]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index array
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Product removed from wishlist',
            'status' => 'removed'
        ]);
    } else {
        // Add to wishlist
        $_SESSION['wishlist'][] = $product_id;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to wishlist',
            'status' => 'added'
        ]);
    }
}