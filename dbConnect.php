<?php
$host = 'localhost';
$db = 'FonXpress';
$user = 'root';
$pass = 'Squ@r30n3';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
