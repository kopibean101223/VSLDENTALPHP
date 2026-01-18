<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {

    $appservice = isset($_POST['appserviceid']) ? trim($_POST['appserviceid']) : '';
    $accountsid = isset($_POST['accountsid']) ? trim($_POST['accountsid']) : '';

    // Validate
    if (empty($appservice) || empty($accountsid)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields (appserviceid or accountsid)"
        ]);
        exit;
    }

    // --- UPDATE NOTIFICATIONS (set is_read = true) ---
    $sql = "
        UPDATE notifications 
        SET is_read = true 
        WHERE appointment_service_id = :appservice 
        AND accounts_id = :accountsid
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':appservice' => $appservice,
        ':accountsid' => $accountsid
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Notification updated successfully"
    ]);

} catch (PDOException $e) {
    error_log("SQL ERROR: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
s