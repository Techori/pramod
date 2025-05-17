<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './_conn.php';

$user_name = $_SESSION['user_name'];

// 1. Auto-delete read notifications older than 24 hours
$conn->query("DELETE FROM notifications WHERE is_read = 1 AND read_at < NOW() - INTERVAL 1 DAY");

// 2. Insert Notification (Only if POST request with insert=true)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert'])) {
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $type = $_POST['type'] ?? '';
    // $user_name = $_POST['user_name'] ?? '';

    if ($title && $message && $user_name) {
        $stmt = $conn->prepare("INSERT INTO notifications (title, message, type, user_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $message, $type, $user_name);
        $stmt->execute();
        echo "Notification inserted";
    } else {
        echo "Missing fields";
    }
    exit;
}

// 3. Mark as Read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $id = $_POST['id'] ?? 0;
    if ($id) {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "Marked as read";
    } else {
        echo "Invalid ID";
    }
    exit;
}

// 4. Get Notifications for Logged-in User
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // $user_name = $_SESSION['user_name'] ?? null;
    if (!$user_name) {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_name = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($notifications);
    exit;
}
?>
