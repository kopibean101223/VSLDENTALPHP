<?php
require 'db_connect.php';
header('Content-Type: application/json');
error_log("faefe");
$appointmentId = isset($_POST['appointmentId']) ? trim($_POST['appointmentId']) : '';
$serviceId = isset($_POST['serviceId']) ? trim($_POST['serviceId']) : '';
$newStatus = "Cancelled"; // or "Accepted"
$header = "Appointment Cancelled!";
$message = "Your appointment has been cancelled!";
error_log("afewe");
try {
    // Start transaction
    $conn->beginTransaction();

    error_log("fawe");
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
error_log("faefafwee");
    $stmt = $conn->prepare($updateSql);
    $stmt->execute([
        ':newStatus' => $newStatus,
        ':appointmentId' => $appointmentId,
        ':serviceId' => $serviceId
    ]);
error_log("faewfeaw");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $conn->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "No matching appointment found."
        ]);
        exit;
    }
error_log("faefaweaefe");
    $appointmentServiceId = $row['appointment_service_id'];
    $patientId = $row['accounts_id'];

    // 2. Insert notification for the patient
    $notifSql = "
        INSERT INTO notifications (accounts_id, appointment_service_id, header ,message)
        VALUES (:patientId, :appointmentServiceId, :header,:message)
    ";
    $notifStmt = $conn->prepare($notifSql);
    $notifStmt->execute([
        ':patientId' => $patientId,
        ':appointmentServiceId' => $appointmentServiceId,
        ':message' => $message,
        ':header'=> $header
    ]);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Appointment accepted and notification sent."
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
