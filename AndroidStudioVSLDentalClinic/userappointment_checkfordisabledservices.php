<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $selectedDate = $_POST['selectedDate'] ?? null;
    $status = "Disabled";


    $sql = "
        SELECT 
           service_id
        FROM service_status
        WHERE date = :selectedDate AND status = :status
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':selectedDate', $selectedDate);
    $stmt->bindValue(':status', $status);


    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data"   => $result
    ]);

} catch (PDOException $e) {

    error_log("SQL ERROR: " . $e->getMessage());

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
