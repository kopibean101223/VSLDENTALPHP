<?php
header('Content-Type: application/json');

try {
    require 'db_connect.php'; // $conn should be a PDO instance

    // Get POST data
    $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
    $appointmentdate = isset($_POST['appointmentdate']) ? $_POST['appointmentdate'] : null; // YYYY-MM-DD
    $appointmenttime = isset($_POST['appointmenttime']) ? $_POST['appointmenttime'] : null; // HH:MM:SS
    $services = isset($_POST['services']) ? json_decode($_POST['services'], true) : [];

    if (!$patient_id || !$appointmentdate || !$appointmenttime || empty($services)) {
        throw new Exception("Missing required fields.");
    }

    // 🔹 GET FIRSTNAME AND LASTNAME FROM PATIENTS TABLE
    $stmtName = $conn->prepare("SELECT firstname, lastname FROM patients WHERE patient_id = :pid");
    $stmtName->execute([':pid' => $patient_id]);
    $patientInfo = $stmtName->fetch(PDO::FETCH_ASSOC);

    if (!$patientInfo) {
        throw new Exception("Patient not found.");
    }

    $firstname = $patientInfo['firstname'];
    $lastname = $patientInfo['lastname'];

    // Begin transaction
    $conn->beginTransaction();

    // Insert into appointments
    $stmt = $conn->prepare("
        INSERT INTO appointments (patient_id, appointment_date, appointment_time)
        VALUES (:patient_id, :appointmentdate, :appointmenttime)
        RETURNING appointment_id
    ");
    $stmt->execute([
        ':patient_id' => $patient_id,
        ':appointmentdate' => $appointmentdate,
        ':appointmenttime' => $appointmenttime
    ]);

    $appointment_id = $stmt->fetchColumn();
    if (!$appointment_id) {
        throw new Exception("Failed to create appointment.");
    }

    // Prepare statement to insert services
    $stmtService = $conn->prepare("
        INSERT INTO appointment_services (appointment_id, service_id, dentist_id, status, created_at)
        VALUES (:appointment_id, :service_id, NULL, 'Pending', NOW())
        RETURNING appointment_service_id
    ");

    // Prepare statement to get service_id from services table
    $stmtGetServiceId = $conn->prepare("SELECT service_id FROM services WHERE servicetype = :service_name");

    $appointment_service_ids = []; // Collect inserted service IDs

    foreach ($services as $service) {
        // Get service_id
        $stmtGetServiceId->execute([':service_name' => $service]);
        $service_id = $stmtGetServiceId->fetchColumn();

        if (!$service_id) {
            throw new Exception("Invalid service type: $service");
        }

        // Insert into appointment_services
        $stmtService->execute([
            ':appointment_id' => $appointment_id,
            ':service_id' => $service_id
        ]);

        $appointment_service_id = $stmtService->fetchColumn();
        $appointment_service_ids[] = $appointment_service_id; // store each ID
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Appointment booked successfully",
        "appointment_id" => $appointment_id,
        "appointment_service_id" => $appointment_service_ids, // array
        "firstname" => $firstname,
        "lastname" => $lastname
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>