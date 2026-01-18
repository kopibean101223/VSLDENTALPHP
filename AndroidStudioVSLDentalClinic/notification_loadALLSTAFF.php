<?php
header('Content-Type: application/json');
require 'db_connect.php';    // make sure $pdo is a PDO instance

try {
    // Fetch all staff account IDs
    $query = "SELECT accounts_id FROM staffs"; // table name is staff
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        // Return as an array of IDs
        $ids = array_column($rows, 'accounts_id');
        echo json_encode([
            "status" => "success",
            "data" => $ids
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No staff found"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
