<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Appointment details
    $dentist_id = isset($_POST['dentistID']) ? $_POST['dentistID'] : null;
    $patient_id = isset($_POST['userID']) ? $_POST['userID'] : null;
    $appointment_time = isset($_POST['appTime']) ? $_POST['appTime'] : null;
    $appointment_date = isset($_POST['appDate']) ? $_POST['appDate'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;

    $services = [
        ['Teeth Cleaning', ],
        ['Filling']
    ];

    // Start transaction
    $pdo->beginTransaction();

    // Insert appointment
    $stmt = $pdo->prepare("
        INSERT INTO appointments (dentist_id, patient_id, appointmenttime, appointmentdate, status)
        VALUES (:dentist_id, :patient_id, :appointmenttime, :appointmentdate, :status)
        RETURNING appointment_id
    ");
    $stmt->execute([
        ':dentist_id' => $dentist_id,
        ':patient_id' => $patient_id,
        ':appointmenttime' => $appointment_time,
        ':appointmentdate' => $appointment_date,
        ':status' => $status
    ]);

    // Get the generated appointment_id
    $appointment_id = $stmt->fetchColumn();

    // Insert services
    $stmt_service = $pdo->prepare("
        INSERT INTO services (appointment_id, servicetype, cost)
        VALUES (:appointment_id, :servicetype, :cost)
    ");

    foreach ($services as $service) {
        $stmt_service->execute([
            ':appointment_id' => $appointment_id,
            ':servicetype' => $service[0],
            ':cost' => $service[1]
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo "Appointment and services added successfully! Appointment ID: $appointment_id";

} catch (PDOException $e) {
    // Rollback if something fails
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
v