<?php
require 'db_connect.php';
header('Content-Type: application/json');

$appointmentId = isset($_POST['appointmentId']) ? trim($_POST['appointmentId']) : '';
$serviceId = isset($_POST['serviceId']) ? trim($_POST['serviceId']) : '';
$newStatus = "Cancelled"; 

try {
    // Start transaction
    $conn->beginTransaction();

    // Update appointment_services status and return values needed for notification
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
            a.accounts_id,
            p.firstname,
            p.lastname,
            app.appointment_time,
            app.appointment_date
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

    // Commit only the update
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Appointment status updated.",
        "data" => [
            "appointment_service_id" => $row['appointment_service_id'],
            "patient_id" => $row['patient_id'],
            "accounts_id" => $row['accounts_id'],
            "appointment_time" => $row['appointment_time'],
            "appointment_date" => $row['appointment_date'],
            "firstname"  => $row['firstname'],
            "lastname"  => $row['lastname']
        ]
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
