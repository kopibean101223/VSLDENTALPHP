<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $sql = "
        SELECT 
            a.appointment_id,
            a.dentist_id,
            a.patient_id,
            a.appointmenttime,
            a.appointmentdate,
            a.status,
            p.firstname AS patient_firstname,
            p.lastname AS patient_lastname,
            p.contactnumber AS patient_contact,
            p.email AS patient_email
        FROM appointments a
        INNER JOIN patients p ON a.patient_id = p.patient_id
        ORDER BY a.appointmentdate, a.appointmenttime
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>