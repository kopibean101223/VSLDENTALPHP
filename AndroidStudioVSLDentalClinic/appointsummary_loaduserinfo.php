<?php
header('Content-Type: application/json');
require 'db_connect.php';

$patientId = isset($_POST['patientId']) ? $_POST['patientId'] : null;

if ($patientId !== null) {
    $query = "SELECT * FROM patients WHERE patient_id = :patientId";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':patientId', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Patient not found"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "patientId missing"
    ]);
}
?>
