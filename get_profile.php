<?php
require __DIR__ . '/config/dbConnect.php';
// Get username from session or GET param
$username = '';
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
$response = [];
if ($username) {
    $stmt = $conn->prepare('SELECT fullname, email, phone, address, gender, username FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
        $response = $result;
    }
}
echo json_encode($response);
?>
