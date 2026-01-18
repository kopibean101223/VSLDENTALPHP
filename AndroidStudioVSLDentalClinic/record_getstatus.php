<?php
header('Content-Type: application/json');
require 'db_connect.php'; // $conn is PDO instance

// Enable full error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);

try {
    // Get POST data
    $appointmentid = isset($_POST['appointmentid']) && $_POST['appointmentid'] !== '' ? $_POST['appointmentid'] : null;
    $serviceid = isset($_POST['serviceid']) && $_POST['serviceid'] !== '' ? $_POST['serviceid'] : null;

    error_log("POST data received: " . print_r($_POST, true));
    error_log("Parsed appointmentid: $appointmentid, serviceid: $serviceid");

    // Check that both values are provided
    if (!$appointmentid || !$serviceid) {
        error_log("Missing appointmentid or serviceid");
        echo json_encode([
            "status" => "error",
            "message" => "Appointment ID or Service ID missing"
        ]);
        exit;
    }

    // Prepare SQL query
    $query = "SELECT status FROM appointment_services 
              WHERE appointment_id = :appointmentid 
              AND service_id = :serviceid";
   

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':appointmentid', $appointmentid);
    $stmt->bindValue(':serviceid', $serviceid);

    // Execute and log any exceptions
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        error_log("SQL execution failed: " . print_r($errorInfo, true));
        echo json_encode([
            "status" => "error",
            "message" => "Database query failed"
        ]);
        exit;
    }

    // Fetch result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("Query result: " . print_r($row, true));

    if ($row) {
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        error_log("No appointment service found for appointmentid=$appointmentid, serviceid=$serviceid");
        echo json_encode([
            "status" => "error",
            "message" => "No appointment service found"
        ]);
    }

} catch (PDOException $e) {
    error_log("PDO Exception: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Database error"
    ]);
} catch (Exception $e) {
    error_log("General Exception: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Server error"
    ]);
}
exit;
?>
