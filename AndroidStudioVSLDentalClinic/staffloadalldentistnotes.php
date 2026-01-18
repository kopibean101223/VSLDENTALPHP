<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {

$patientid = isset($_POST['patientid']) ? trim($_POST['patientid']) : '';

    // Base SQL
    $sql = "
    SELECT  
       n.message,
       n.appointment_service_id,
       n.created_at,
       n.patient_id,
       appser.service_id,
       appser.dentist_id,
       appser.appointment_id,
       d.firstname,
       d.lastname,
       s.servicetype
    FROM note n
    INNER JOIN appointment_services appser ON appser.appointment_service_id = n.appointment_service_id
    INNER JOIN dentists d ON d.dentist_id = appser.dentist_id
    INNER JOIN services s ON s.service_id = appser.service_id
    WHERE n.patient_id = :patientid
    ORDER BY n.created_at DESC
";

 
    $stmt = $conn->prepare($sql);
   $stmt->bindValue(':patientid', $patientid, PDO::PARAM_STR);
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
