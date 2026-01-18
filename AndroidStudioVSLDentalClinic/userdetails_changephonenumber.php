<?php
require 'db_connect.php'; 

$userid = $_POST['userid'] ?? null;
$phonenumber = $_POST['phonenumber'] ?? null;


try {
    $sql = "UPDATE accounts SET contactnumber = :phonenumber WHERE accounts_id = :userid";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':phonenumber', $phonenumber, PDO::PARAM_STR); 
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