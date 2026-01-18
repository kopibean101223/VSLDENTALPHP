<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $appointmentId = isset($_POST['appointmentId']) ;
    $serviceId = isset($_POST['serviceId']) ;

    if (!$appointmentId || !$serviceId) {
        throw new Exception("Appointment ID and Service ID are required.");
    }

    $sql = "
        SELECT 
            aps.status,
            s.servicetype,
            a.appointment_date,
            a.appointment_time,
            r.rating,
            r.note,
            p.amount,
            p.payment_method
        FROM appointment_services aps
        INNER JOIN services s ON aps.service_id = s.service_id
        INNER JOIN appointments a ON aps.appointment_id = a.appointment_id
        LEFT JOIN reviews r ON r.appointment_service_id = aps.appointment_service_id
        LEFT JOIN payments p ON p.appointment_service_id = aps.appointment_service_id
        WHERE aps.appointment_id = :appointmentId AND aps.service_id = :serviceId
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':appointmentId', $appointmentId, PDO::PARAM_INT);
    $stmt->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);

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
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
    ]);
}
?>
