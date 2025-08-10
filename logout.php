<?php
session_start();
require __DIR__ . '/config/dbConnect.php';
session_unset();
session_destroy();
echo json_encode(["success" => true]);
?>
