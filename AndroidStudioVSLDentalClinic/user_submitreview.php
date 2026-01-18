<?php
header('Content-Type: application/json');

try {
    require 'db_connect.php'; // $conn should be a PDO instance

    // Get POST data
    $appointment_service_id = isset($_POST['appointment_service_id']) ? intval($_POST['appointment_service_id']) : null;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null; // 1-5 integer
    $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
    $note = isset($_POST['note']) ? $_POST['note'] : ''; // string note

    // Validate required fields
    if (!$appointment_service_id || !$patient_id || !$rating) {
        throw new Exception("Missing required fields.");
    }

    // Convert note to JSON if it's an array
    if (is_array($note)) {
        $note = json_encode($note);
    }

    // Insert into reviews
    $stmt = $conn->prepare("
        INSERT INTO reviews (appointment_service_id, patient_id, rating, note, time_commented)
        VALUES (:appointment_service_id, :patient_id, :rating, :note, NOW())
    ");
    $stmt->execute([
        ':appointment_service_id' => $appointment_service_id,
        ':patient_id' => $patient_id,
        ':rating' => $rating,
        ':note' => $note
    ]);

    // Get last inserted review ID
    $review_id = $conn->lastInsertId();

    echo json_encode([
        "status" => "success",
        "message" => "Review submitted successfully",
        "review_id" => $review_id,
        "appointment_service_id" => $appointment_service_id
    ]);

} catch (Exception $e) {
    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
    