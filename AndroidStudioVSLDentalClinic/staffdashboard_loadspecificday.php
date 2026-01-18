<?php
header('Content-Type: application/json');
require 'db_connect.php'; // $conn is PDO instance

$selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : null;
$status = "Scheduled";

// Debug: log received POST data
error_log("DEBUG: selectedDate = " . $selectedDate);

if ($selectedDate) {
    $query = "
        SELECT 
            a.appointment_time,
            a.appointment_date,
            a.appointment_id,
            app.appointment_service_id,
            app.service_id,
            app.status,
            s.servicetype AS services
        FROM appointments a
        INNER JOIN appointment_services app ON app.appointment_id = a.appointment_id
        INNER JOIN services s ON s.service_id = app.service_id
        WHERE a.appointment_date = :selectedDate 
          AND app.status = :status
        ORDER BY a.appointment_time ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':selectedDate', $selectedDate, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    // Debug: log prepared statement
    error_log("DEBUG: SQL prepared: " . $query);

    try {
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: log number of rows fetched
        error_log("DEBUG: Rows fetched = " . count($rows));

        if ($rows) {
            echo json_encode([
                "status" => "success",
                "data" => $rows,
                "debug" => "Fetched " . count($rows) . " rows"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No appointments found",
                "debug" => "Query executed but returned zero rows"
            ]);
        }
    } catch (PDOException $e) {
        // Debug: log SQL error
        error_log("DEBUG: PDOException: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => "Database error",
            "debug" => $e->getMessage()
        ]);
    }

} else {
    // Debug: log missing selectedDate
    error_log("DEBUG: selectedDate missing in POST");
    echo json_encode([
        "status" => "error",
        "message" => "selectedDate missing",
        "debug" => "POST parameter 'selectedDate' not received"
    ]);
}
?>
