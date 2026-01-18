<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $searchterm = isset($_POST['searchterm']) ? trim($_POST['searchterm']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

   
// BASE QUERY — used for BOTH cases
$sql = "
    SELECT 
        c.appointment_id,
        c.patient_id,
        c.appointment_time,
        c.appointment_date,
        e.status,
        a.created_at AS creationdate,
        p.firstname AS patient_firstname,
        p.lastname AS patient_lastname,
        a.contactnumber AS patient_contact,
        a.email AS patient_email,
        s.servicetype,  
        s.service_id 
    FROM appointments c
    INNER JOIN patients p ON c.patient_id = p.patient_id
    INNER JOIN accounts a ON p.accounts_id = a.accounts_id
    LEFT JOIN appointment_services e ON e.appointment_id = c.appointment_id
    LEFT JOIN services s ON s.service_id = e.service_id
    WHERE 1=1
";

$params = [];

// ================================
// CASE 1: NO SEARCH TERM → apply status filter
// ================================
if ($searchterm == '') {

    if ($status !== "") {
        $sql .= " AND e.status = :status";
        $params['status'] = $status;
    }

// ================================
// CASE 2: WITH SEARCH TERM → ignore status
// ================================
} else {

    if ($status !== "") {
        $sql .= " AND e.status = :status";
        $params['status'] = $status;
    }
    
    $sql .= "
        AND (
            s.servicetype ILIKE :searchterm 
        )
    ";
    $params['searchterm'] = "%$searchterm%";
}

// ORDER BY most recent
$sql .= " ORDER BY c.appointment_date ASC, c.appointment_time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);

} catch (PDOException $e) {
    error_log("SQL ERROR: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
