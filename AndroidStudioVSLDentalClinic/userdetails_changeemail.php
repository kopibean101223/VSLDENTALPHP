<?php
require 'db_connect.php'; // must return a $pdo PDO instance

$userid = $_POST['userid'] ?? null;
$email = $_POST['email'] ?? null;


try {
    $sql = "UPDATE accounts SET email = :email WHERE accounts_id = :userid";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':email', $email, PDO::PARAM_STR); 
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