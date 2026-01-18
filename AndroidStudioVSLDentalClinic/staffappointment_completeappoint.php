<?php
require 'db_connect.php';

$appointmentId = $_POST['appointmentId'] ?? null;
$serviceId = $_POST['serviceId'] ?? null;
$dentistId = $_POST['dentistId'] ?? null;
$serviceStatus = "Completed";
$paymentAmount = $_POST['paymentAmount'] ?? null;
$paymentMethod = $_POST['paymentMethod'] ?? null; 
$header = "Appointment Complete!";

try {
    error_log("DEBUG: Starting update process...");
    if (!$appointmentId || !$serviceId) {
        throw new Exception("Missing appointmentId or serviceId.");
    }
    error_log("DEBUG: appointmentId=$appointmentId, serviceId=$serviceId, dentistId=$dentistId, paymentAmount=$paymentAmount, paymentMethod=$paymentMethod");

    $conn->beginTransaction();

    // 1️⃣ Update appointment_services
    $stmt = $conn->prepare("
        UPDATE appointment_services aps
        SET dentist_id = :dentistId,
            status = :serviceStatus
        FROM appointments a
        WHERE aps.appointment_id = :appointmentId
          AND aps.service_id = :serviceId
          AND aps.appointment_id = a.appointment_id
        RETURNING aps.appointment_service_id, a.patient_id
    ");
    $stmt->execute([
        ':dentistId' => $dentistId,
        ':serviceStatus' => $serviceStatus,
        ':appointmentId' => $appointmentId,
        ':serviceId' => $serviceId
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("DEBUG: Update result row=" . print_r($row, true));
    if (!$row) {
        throw new Exception("No appointment_service found for the given appointmentId and serviceId.");
    }

    $appointmentServiceId = $row['appointment_service_id'];
    $patientId = $row['patient_id'];

    // 2️⃣ Insert into payments
    error_log($paymentAmount);
    error_log($paymentMethod);

    if ($paymentAmount !== null && $paymentMethod !== null) {
        if (!is_numeric($paymentAmount)) {
            throw new Exception("Payment amount must be numeric.");
        }

        $stmt = $conn->prepare("
            INSERT INTO payments (appointment_service_id, amount, payment_method)
            VALUES (:appointmentServiceId, :amount, :paymentMethod)
        ");
        $stmt->execute([
            ':appointmentServiceId' => $appointmentServiceId,
            ':amount' => $paymentAmount,
            ':paymentMethod' => $paymentMethod
        ]);

        error_log("DEBUG: Payment insert affected rows=" . $stmt->rowCount());
        if ($stmt->rowCount() === 0) {
            throw new Exception("Payment insert failed.");
        }
    } else {
        error_log("DEBUG: Skipping payment insert (no amount or method)");
    }

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Appointment updated and payment added successfully.',
        'appointmentServiceId' => $appointmentServiceId,
        'patientId' => $patientId
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("DEBUG: Exception caught: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
