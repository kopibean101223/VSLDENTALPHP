<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {



    // Base SQL
    $sql = "
        SELECT 
            c.appointment_id,
            c.patient_id,
            c.appointment_time,
            c.appointment_date,
            e.status,
            a.created_at AS creationdate,
            p.firstname AS patient_firstname,
            p.lastname AS patient_lastname,
            a.contactnumber AS patient_contact,
            a.email AS patient_email,
            s.servicetype AS services,  
            s.service_id 
        FROM appointments c
        INNER JOIN patients p ON c.patient_id = p.patient_id
        INNER JOIN accounts a ON p.accounts_id = a.accounts_id
        LEFT JOIN appointment_services e ON e.appointment_id = c.appointment_id
        LEFT JOIN services s ON s.service_id = e.service_id
        WHERE e.status = 'Pending'
    ";


 
    $sql .= " ORDER BY c.appointment_date, c.appointment_time LIMIT 3";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
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
