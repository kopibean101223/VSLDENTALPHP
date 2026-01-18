<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {

$appservice = isset($_POST['appserviceid']) ? trim($_POST['appserviceid']) : '';

    // Base SQL
   $sql = "
    SELECT 
        *
    FROM appointment_services WHERE appointment_service_id = :appservice
";

    $stmt = $conn->prepare($sql);

    $stmt->execute([':appservice' => $appservice]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
