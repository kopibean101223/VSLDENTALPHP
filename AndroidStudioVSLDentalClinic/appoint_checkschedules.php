<?php
header('Content-Type: application/json');
require 'db_connect.php';

$selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : null;

$status1 = "Pending";
$status2 = "Scheduled";

if (!$selectedDate) {
    echo json_encode([
        "status" => "error",
        "message" => "selectedDate missing"
    ]);
    exit;
}

try {
    $query = "
        SELECT a.appointment_time
        FROM appointments a
        INNER JOIN appointment_services appser 
            ON appser.appointment_id = a.appointment_id
        WHERE 
            a.appointment_date = :selectedDate
            AND (appser.status = :status1 OR appser.status = :status2)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':selectedDate', $selectedDate, PDO::PARAM_STR);
    $stmt->bindValue(':status1', $status1, PDO::PARAM_STR);
    $stmt->bindValue(':status2', $status2, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
