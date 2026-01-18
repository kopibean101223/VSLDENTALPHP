<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {



    // Base SQL
   $sql = "
    SELECT 
        p.firstname,
        p.lastname,
        p.accounts_id,
        p.patient_id,
        a.email
    FROM patients p
    INNER JOIN accounts a ON a.accounts_id = p.accounts_id
    ORDER BY p.accounts_id DESC
    LIMIT 3
";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
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
