<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $rating = isset($_POST['rating']) ? trim($_POST['rating']) : null;

    // Base SQL
    $sql = "
        SELECT 
            r.review_id,
            r.rating,
            r.note,
            r.time_commented,
            r.appointment_service_id,
            r.patient_id,
            p.firstname,    
            p.lastname,
            s.servicetype
        FROM reviews r
        JOIN patients p ON r.patient_id = p.patient_id
        JOIN appointment_services aps ON r.appointment_service_id = aps.appointment_service_id
        JOIN services s ON aps.service_id = s.service_id
    ";

    // Add filter only if a rating was given
    //if (!empty($rating)) {
      //  $sql .= " WHERE r.rating = :rating";
    //  }

    $stmt = $conn->prepare($sql);

    if (!empty($rating)) {
        //$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);

} catch (PDOException $e) {

    // Log the error separately
    error_log("Review Fetch Error: " . $e->getMessage());

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
