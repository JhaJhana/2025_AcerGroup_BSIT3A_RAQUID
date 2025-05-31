<?php
session_start();
include 'db.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $karat = $_POST['karat'];

    // Image handling
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];

    $uploadDir = 'uploads/';
    $imagePath = $uploadDir . basename($imageName);

    // Move image
    if (move_uploaded_file($imageTmp, $imagePath)) {
        // Save to DB
        $stmt = $conn->prepare("INSERT INTO products (user_id, product_name, price, category, karat, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsss", $user_id, $name, $price, $category, $karat, $imageName);

        if ($stmt->execute()) {
            echo "Item uploaded successfully.";
        } else {
            echo "Error saving to database: " . $stmt->error;
        }
    } else {
        echo "Image upload failed.";
    }
}
?>
