<?php
function createNotification($pdo, $user_id, $title, $message, $type = 'info', $due_date = null) {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, due_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $message, $type, $due_date]);
}
?>
