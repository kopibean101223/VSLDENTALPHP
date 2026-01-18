<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}


$allowed_roles = ['Patient', 'Doctor', 'Staff'];


if (!in_array($_SESSION['role'], $allowed_roles)) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

echo json_encode(["status" => "success", "message" => "Authorized"]);
?>
