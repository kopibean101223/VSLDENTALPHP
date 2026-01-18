<?php
require 'db_connect.php';
header('Content-Type: application/json');

$roles = $_POST['role'];
try{
if (!empty($roles)) {
        $sql = "SELECT accounts_id, email, role 
                FROM accounts 
                WHERE role = :role 
                ORDER BY created_at ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':role', $roles);
    } else {
        $sql = "SELECT accounts_id, email, role 
                FROM accounts 
                ORDER BY created_at ASC";
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>