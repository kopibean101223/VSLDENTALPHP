<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Input
$appointmentId = isset($_POST['appointmentId']) ? trim($_POST['appointmentId']) : '';
$serviceId     = isset($_POST['serviceId']) ? trim($_POST['serviceId']) : '';

try {

    $sql = "
        SELECT 
            p.firstname,
            p.lastname,
            p.accounts_id,
            p.patient_id,
            a.email,
            s.servicetype,
            app.appointment_time,
            app.appointment_date,
            appser.status,
            appser.appointment_service_id,
            pay.amount,
            pay.payment_method,
            n.message
        FROM appointment_services appser
        INNER JOIN appointments app 
            ON app.appointment_id = appser.appointment_id
        INNER JOIN services s 
            ON s.service_id = appser.service_id
        INNER JOIN patients p 
            ON p.patient_id = app.patient_id
        INNER JOIN accounts a 
            ON p.accounts_id = a.accounts_id
        INNER JOIN payments pay
        ON pay.appointment_service_id = appser.appointment_service_id
        LEFT JOIN note n
        ON n.appointment_service_id = appser.appointment_service_id
        WHERE appser.appointment_id = :appointmentId
          AND appser.service_id = :serviceId
    ";


$stmt = $conn->prepare($sql);
$stmt->bindParam(":appointmentId", $appointmentId);
$stmt->bindParam(":serviceId", $serviceId);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC); // <-- FIXED


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
