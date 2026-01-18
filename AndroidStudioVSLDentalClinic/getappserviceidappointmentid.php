<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {

$accountsid = isset($_POST['patientid']) ? trim($_POST['patientid']) : '';

    // Base SQL
   $sql = "
    SELECT 
        *
    FROM notifications WHERE accounts_id = :accountsid
";

    $stmt = $conn->prepare($sql);

    $stmt->execute([':accountsid' => $accountsid]);
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
