<?php
header('Content-Type: application/json');

try {
    require 'db_connect.php'; // $conn must be a PDO instance

    // Enable exceptions for PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $appointment_service_id = isset($_POST['appointment_service_id']) ? intval($_POST['appointment_service_id']) : null;
    $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
    $note = isset($_POST['note']) ? $_POST['note'] : '';

    // Validate required fields
    if (!$appointment_service_id || !$patient_id) {
        throw new Exception("Missing required fields (appointment_service_id or patient_id).");
    }

    // Convert note to JSON if it's an array
    if (is_array($note)) {
        $note = json_encode($note);
    }

    // Start transaction
    $conn->beginTransaction();

    // Insert note using PDO prepared statement
    $stmt = $conn->prepare("
        INSERT INTO note (appointment_service_id, patient_id, message, created_at)
        VALUES (:appointment_service_id, :patient_id, :note, NOW())
    ");
    $stmt->execute([
        ':appointment_service_id' => $appointment_service_id,
        ':patient_id' => $patient_id,
        ':note' => $note
    ]);

    // Commit transaction
    $conn->commit();

    $note_id = $conn->lastInsertId();

    echo json_encode([
        "status" => "success",
        "message" => "Note submitted successfully",
        "note_id" => $note_id,
        "appointment_service_id" => $appointment_service_id
    ]);

} catch (Exception $e) {
    // Rollback if transaction started
    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
