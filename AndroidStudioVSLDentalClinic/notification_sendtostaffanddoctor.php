<?php
header('Content-Type: application/json');
require 'db_connect.php'; 

$recipientId = $_POST['recipient_id'] ?? null;
$message = $_POST['message'] ?? null;
$header = $_POST['header'] ?? null;
$appserviceid_raw = $_POST['appserviceid'] ?? null;

if ($appserviceid_raw !== null) {
    $decoded = json_decode($appserviceid_raw, true);
    if (is_array($decoded) && count($decoded) > 0) {
        $appserviceid = intval($decoded[0]);
    } else {
        $appserviceid = intval($appserviceid_raw);
    }
} else {
    $appserviceid = null; // can be null
}


error_log("Received data - recipientId: $recipientId, message: $message, header: $header, appserviceid: $appserviceid");

if (!$recipientId || !$message) {
    error_log("Error: recipient_id or message missing");
    echo json_encode([
        'status' => 'error',
        'message' => 'recipient_id or message missing'
    ]);
    exit;
}

try {
    $sql = "INSERT INTO notifications (accounts_id, message, appointment_service_id, header, created_at) 
            VALUES (:accounts_id, :message, :appserviceid, :header, NOW()) RETURNING notification_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':accounts_id', $recipientId);
    $stmt->bindParam(':appserviceid', $appserviceid);
    $stmt->bindParam(':header', $header);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    $notification_id = $stmt->fetchColumn(); // get inserted notification ID

    if ($notification_id) {
        error_log("Notification inserted successfully. ID: $notification_id");
        echo json_encode([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'notification_id' => $notification_id
        ]);
    } else {
        error_log("Notification insert failed.");
        echo json_encode([
            'status' => 'error',
            'message' => 'Notification not inserted'
        ]);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
