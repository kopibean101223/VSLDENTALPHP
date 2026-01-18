<?php
require 'db_connect.php';
header('Content-Type: application/json');

$appointmentId = isset($_POST['appointmentId']) ? trim($_POST['appointmentId']) : '';
$serviceId = isset($_POST['serviceId']) ? trim($_POST['serviceId']) : '';
$newStatus = "Scheduled"; // or "Accepted"

try {
    // Start transaction
    $conn->beginTransaction();

    // 1. Update the appointment_services status
    $updateSql = "
        UPDATE appointment_services AS appser
        SET status = :newStatus
        FROM appointments AS app
        JOIN patients p ON p.patient_id = app.patient_id
        JOIN accounts a ON a.accounts_id = p.accounts_id
        WHERE appser.appointment_id = :appointmentId
          AND appser.service_id = :serviceId
          AND appser.appointment_id = app.appointment_id
        RETURNING 
            appser.appointment_service_id,
            app.patient_id,
            a.accounts_id
    ";

    $stmt = $conn->prepare($updateSql);
    $stmt->execute([
        ':newStatus' => $newStatus,
        ':appointmentId' => $appointmentId,
        ':serviceId' => $serviceId
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $conn->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "No matching appointment found."
        ]);
        exit;
    }

    $appointmentServiceId = $row['appointment_service_id'];
    $patientId = $row['accounts_id'];

    // Commit transaction
    $conn->commit();

    // Return the values needed for notifications
    echo json_encode([
        "status" => "success",
        "appointment_service_id" => $appointmentServiceId,
        "patient_id" => $patientId
    ]);

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("SQL ERROR: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
