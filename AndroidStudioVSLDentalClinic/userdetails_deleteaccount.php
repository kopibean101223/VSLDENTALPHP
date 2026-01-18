<?php
require 'db_connect.php'; 

$userid = $_POST['userid'] ?? null;
$status = "Disabled";


try {
    $sql = "UPDATE accounts SET status = :status WHERE accounts_id = :userid";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':status', $status, PDO::PARAM_STR); 
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Patient gender updated successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}