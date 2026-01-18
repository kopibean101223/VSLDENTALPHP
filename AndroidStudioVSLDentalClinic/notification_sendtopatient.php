<?php
require 'db_connect.php';  // PDO connection
header('Content-Type: application/json');

// Get POST data
$patient_id = isset($_POST['recipient_id']) ? trim($_POST['recipient_id']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$header = isset($_POST['header']) ? trim($_POST['header']) : '';
$appserviceid = isset($_POST['appserviceid']) ? trim($_POST['appserviceid']) : '';

if ($patient_id === '' || $message === '' || $header === '' || $appserviceid === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    // Prepare the PDO statement
    $stmt = $conn->prepare("
        INSERT INTO notifications (accounts_id, message, header, appointment_service_id, created_at)
        VALUES (:accounts_id, :message, :header, :appointment_service_id, NOW())
    ");

    // Execute with named parameters
    $stmt->execute([
        ':accounts_id' => $patient_id,
        ':message' => $message,
        ':header' => $header,
        ':appointment_service_id' => $appserviceid
    ]);

    // Check if insert was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notification sent to patient'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send notification'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Exception: ' . $e->getMessage()
    ]);
}
?>
