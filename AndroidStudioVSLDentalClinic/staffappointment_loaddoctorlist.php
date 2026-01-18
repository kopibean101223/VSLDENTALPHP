<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    // Base SQL
   $sql = "
    SELECT 
        *
    FROM dentists
";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);

} catch (PDOException $e) {
    error_log("SQL ERROR: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
