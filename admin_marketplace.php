<?php
include 'db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle item approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    $marketplace_notes = trim(filter_input(INPUT_POST, 'marketplace_notes', FILTER_SANITIZE_STRING));
    $admin_id = $_SESSION['admin_id'];

    if (!$item_id || !$action || !$marketplace_notes) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // First verify the item exists and is pending
            $check_stmt = $conn->prepare("SELECT id, marketplace_status FROM sangla_requests WHERE id = ? AND for_marketplace = 1");
            $check_stmt->execute([$item_id]);
            $item = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                $error = 'Item not found or not marked for marketplace.';
            } else if ($item['marketplace_status'] !== 'pending') {
                $error = 'This item has already been processed.';
            } else {
                if ($action === 'approve') {
                    $marketplace_price = filter_input(INPUT_POST, 'marketplace_price', FILTER_VALIDATE_FLOAT);
                    if (!$marketplace_price || $marketplace_price <= 0) {
                        $error = 'Please enter a valid marketplace price.';
                    } else {
                        $stmt = $conn->prepare("
                            UPDATE sangla_requests 
                            SET marketplace_status = 'approved', 
                                marketplace_price = ?, 
                                marketplace_notes = ?,
                                approved_by = ?,
                                approved_at = CURRENT_TIMESTAMP
                            WHERE id = ?
                        ");
                        $stmt->execute([$marketplace_price, $marketplace_notes, $admin_id, $item_id]);
                        $message = 'Item approved for marketplace!';
                    }
                } else if ($action === 'reject') {
                    $stmt = $conn->prepare("
                        UPDATE sangla_requests 
                        SET marketplace_status = 'rejected', 
                            marketplace_notes = ?,
                            rejected_by = ?,
                            rejected_at = CURRENT_TIMESTAMP
                        WHERE id = ?
                    ");
                    $stmt->execute([$marketplace_notes, $admin_id, $item_id]);
                    $message = 'Item rejected from marketplace.';
                } else {
                    $error = 'Invalid action specified.';
                }
            }
        } catch (PDOException $e) {
            $error = 'An error occurred while processing your request. Please try again.';
            // Log the error for debugging
            error_log("Marketplace action error: " . $e->getMessage());
        }
    }
}

// Fetch items based on status filter
$status_filter = isset($_GET['status']) ? filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING) : 'pending';
$valid_statuses = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = 'pending';
}

$status_condition = $status_filter === 'all' ? "WHERE for_marketplace = 1" : "WHERE for_marketplace = 1 AND marketplace_status = ?";

$stmt = $conn->prepare("
    SELECT sr.*, 
           u.full_name as seller_name,
           a1.full_name as approved_by_name,
           a2.full_name as rejected_by_name
    FROM sangla_requests sr 
    LEFT JOIN users u ON sr.user_id = u.id 
    LEFT JOIN admins a1 ON sr.approved_by = a1.id
    LEFT JOIN admins a2 ON sr.rejected_by = a2.id
    $status_condition 
    ORDER BY sr.created_at DESC
");

if ($status_filter !== 'all') {
    $stmt->execute([$status_filter]);
} else {
    $stmt->execute();
}

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace Management - iPawnshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }

        h1 {
            color: #0e2f1c;
            margin-bottom: 2rem;
        }

        .filters {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .filter-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            background: #0e2f1c;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .filter-button:hover {
            background: #1a5a3f;
        }

        .filter-button.active {
            background: #d4a437;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-details {
            padding: 1.5rem;
        }

        .item-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .item-info {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .item-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0e2f1c;
            margin-bottom: 1rem;
        }

        .item-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .action-button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .approve-button {
            background: #28a745;
            color: white;
        }

        .approve-button:hover {
            background: #218838;
        }

        .reject-button {
            background: #dc3545;
            color: white;
        }

        .reject-button:hover {
            background: #c82333;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background: white;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            color: #0e2f1c;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .save-button {
            background: #28a745;
            color: white;
        }

        .save-button:hover {
            background: #218838;
        }

        .cancel-button {
            background: #6c757d;
            color: white;
        }

        .cancel-button:hover {
            background: #5a6268;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #0e2f1c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .back-button:hover {
            background: #1a5a3f;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-button">← Back to Admin Dashboard</a>
        <h1>Marketplace Management</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="filters">
            <button class="filter-button <?php echo $status_filter === 'pending' ? 'active' : ''; ?>" onclick="window.location.href='?status=pending'">Pending</button>
            <button class="filter-button <?php echo $status_filter === 'approved' ? 'active' : ''; ?>" onclick="window.location.href='?status=approved'">Approved</button>
            <button class="filter-button <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>" onclick="window.location.href='?status=rejected'">Rejected</button>
            <button class="filter-button <?php echo $status_filter === 'all' ? 'active' : ''; ?>" onclick="window.location.href='?status=all'">All</button>
        </div>

        <div class="items-grid">
            <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <?php if ($item['photo']): ?>
                        <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="<?php echo htmlspecialchars($item['item_type']); ?>" class="item-image">
                    <?php else: ?>
                        <img src="images/placeholder.jpg" alt="No image" class="item-image">
                    <?php endif; ?>
                    
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['item_type']); ?></div>
                        <div class="item-info">
                            Brand: <?php echo htmlspecialchars($item['brand']); ?><br>
                            Model: <?php echo htmlspecialchars($item['model']); ?><br>
                            Weight: <?php echo htmlspecialchars($item['grams']); ?>g<br>
                            Original Price: ₱<?php echo number_format($item['orig_price'], 2); ?><br>
                            Seller: <?php echo htmlspecialchars($item['seller_name']); ?>
                        </div>
                        
                        <?php if ($item['marketplace_status'] === 'approved'): ?>
                            <div class="item-price">
                                Marketplace Price: ₱<?php echo number_format($item['marketplace_price'], 2); ?>
                            </div>
                            <?php if ($item['marketplace_notes']): ?>
                                <div class="item-info">
                                    Description: <?php echo htmlspecialchars($item['marketplace_notes']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="item-info">
                                Approved by: <?php echo htmlspecialchars($item['approved_by_name']); ?><br>
                                Approved on: <?php echo date('M d, Y h:i A', strtotime($item['approved_at'])); ?>
                            </div>
                        <?php elseif ($item['marketplace_status'] === 'rejected'): ?>
                            <?php if ($item['marketplace_notes']): ?>
                                <div class="item-info">
                                    Reason: <?php echo htmlspecialchars($item['marketplace_notes']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="item-info">
                                Rejected by: <?php echo htmlspecialchars($item['rejected_by_name']); ?><br>
                                Rejected on: <?php echo date('M d, Y h:i A', strtotime($item['rejected_at'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="item-status status-<?php echo strtolower($item['marketplace_status']); ?>">
                            <?php echo ucfirst($item['marketplace_status']); ?>
                        </div>

                        <?php if ($item['marketplace_status'] === 'pending'): ?>
                            <div class="action-buttons">
                                <button class="action-button approve-button" onclick="openApproveModal(<?php echo $item['id']; ?>)">Approve</button>
                                <button class="action-button reject-button" onclick="openRejectModal(<?php echo $item['id']; ?>)">Reject</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Approve Item for Marketplace</h2>
                <button class="close-modal" onclick="closeModal('approveModal')">&times;</button>
            </div>
            <form id="approveForm" method="POST">
                <input type="hidden" name="item_id" id="approveItemId">
                <input type="hidden" name="action" value="approve">
                
                <div class="form-group">
                    <label for="marketplace_price">Marketplace Price:</label>
                    <input type="number" id="marketplace_price" name="marketplace_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="marketplace_notes">Description/Notes:</label>
                    <textarea id="marketplace_notes" name="marketplace_notes" required></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="cancel-button" onclick="closeModal('approveModal')">Cancel</button>
                    <button type="submit" class="save-button">Approve Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reject Item from Marketplace</h2>
                <button class="close-modal" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <form id="rejectForm" method="POST">
                <input type="hidden" name="item_id" id="rejectItemId">
                <input type="hidden" name="action" value="reject">
                
                <div class="form-group">
                    <label for="reject_notes">Reason for Rejection:</label>
                    <textarea id="reject_notes" name="marketplace_notes" required></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="cancel-button" onclick="closeModal('rejectModal')">Cancel</button>
                    <button type="submit" class="save-button" style="background: #dc3545;">Reject Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(itemId) {
            document.getElementById('approveItemId').value = itemId;
            document.getElementById('approveModal').style.display = 'block';
        }

        function openRejectModal(itemId) {
            document.getElementById('rejectItemId').value = itemId;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html> 