<?php
// update_profile.php
// Handles profile update including password change
require_once __DIR__ . '/config/dbConnect.php';
session_start();

$response = ["success" => false, "message" => "Unknown error."];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = '';
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate captcha (optional, add if needed)
    // $captcha = $_POST['captcha'] ?? '';
    // ...

    // Validate required fields
    if (!$username || !$fullname || !$email || !$phone || !$address || !$gender) {
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit;
    }

    // Update profile info
    $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, address=?, gender=? WHERE username=?");
    $stmt->bind_param("sssss", $fullname, $phone, $address, $gender, $username);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated.';
    } else {
        $response['message'] = 'Failed to update profile.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // Handle password change if requested
    if ($current_password && $new_password && $confirm_password) {
        if ($new_password !== $confirm_password) {
            $response['success'] = false;
            $response['message'] = 'New passwords do not match.';
            echo json_encode($response);
            exit;
        }
        // Check current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed);
        if ($stmt->fetch() && password_verify($current_password, $hashed)) {
            $stmt->close();
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $stmt2->bind_param("ss", $new_hashed, $username);
            if ($stmt2->execute()) {
                $response['success'] = true;
                $response['message'] .= ' Password updated.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Failed to update password.';
            }
            $stmt2->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'Current password is incorrect.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
