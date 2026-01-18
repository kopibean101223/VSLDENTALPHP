<?php
header('Content-Type: application/json');
require 'db_connect.php'; // make sure $conn is PDO instance

try {
    $query = "SELECT * FROM services";
    $stmt = $conn->prepare($query);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        echo json_encode([
            "status" => "success",
            "data" => $rows
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
