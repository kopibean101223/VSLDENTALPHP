<?php
header('Content-Type: application/json');
require 'db_connect.php';

$accounts_id = $_POST['accounts_id'] ?? null;

if (!$accounts_id) {
    echo json_encode(["status" => "error", "message" => "Missing user ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT profilepic FROM accounts WHERE accounts_id = :id");
$stmt->execute([':id' => $accounts_id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "success",
    "profilepic" => $data["profilepic"] ?? ""
]);
