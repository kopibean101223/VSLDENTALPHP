<?php
require 'db_connect.php';
header('Content-Type: application/json');
try{
$patientid = isset($_POST['patientid']) ? $_POST['patientid'] : null;
$status = 'Scheduled'; 
error_log("POST RECEIVED: " . json_encode($_POST));


 $sql = "
    SELECT
    a.appointment_time,
    a.appointment_date,
    a.appointment_id,
    appser.service_id,
    appser.status,
    s.servicetype AS services
FROM appointments a
INNER JOIN appointment_services appser 
      ON appser.appointment_id = a.appointment_id
INNER JOIN services s 
      ON s.service_id = appser.service_id
WHERE a.patient_id = :patientid 
  AND appser.status = :status
ORDER BY a.appointment_date, a.appointment_time ASC
LIMIT 1";


    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':patientid', $patientid, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);


 
    $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result,
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
       
    ]);
   
}







?> 