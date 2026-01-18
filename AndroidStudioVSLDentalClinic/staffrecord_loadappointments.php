<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get search term and status safely
$searchterm = isset($_POST['searchterm']) ? trim($_POST['searchterm']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
error_log($status);
try {
    if ($searchterm == "" && $status == "") {
        // No search term, no status: fetch all
        $sql = "
            SELECT 
                p.firstname,
                p.lastname,
                p.accounts_id,
                p.patient_id,
                a.email,
                a.status    
            FROM patients p
            INNER JOIN accounts a ON a.accounts_id = p.accounts_id
            ORDER BY p.accounts_id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } elseif ($searchterm != "" && $status == "") {
        // Search term provided, no status filter
        $sql = "
            SELECT 
                p.firstname,
                p.lastname,
                p.accounts_id,
                p.patient_id,
                a.email,
                a.status
            FROM patients p
            INNER JOIN accounts a ON a.accounts_id = p.accounts_id
            WHERE p.firstname ILIKE :searchterm 
               OR p.lastname ILIKE :searchterm 
               OR a.email ILIKE :searchterm
            ORDER BY p.accounts_id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['searchterm' => "%$searchterm%"]);
    } elseif ($searchterm == "" && $status != "") {
        // Status filter only
        $sql = "
            SELECT 
                p.firstname,
                p.lastname,
                p.accounts_id,
                p.patient_id,
                a.email,
                a.status
            FROM patients p
            INNER JOIN accounts a ON a.accounts_id = p.accounts_id
            WHERE a.status = :status
            ORDER BY p.accounts_id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['status' => $status]);
    } else {
        // Both search term and status
        $sql = "
            SELECT 
                p.firstname,
                p.lastname,
                p.accounts_id,
                p.patient_id,
                a.email,
                a.status
            FROM patients p
            INNER JOIN accounts a ON a.accounts_id = p.accounts_id
            WHERE (p.firstname ILIKE :searchterm 
               OR p.lastname ILIKE :searchterm 
               OR a.email ILIKE :searchterm)
              AND a.status = :status
            ORDER BY p.accounts_id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'searchterm' => "%$searchterm%",
            'status' => $status
        ]);
    }

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
