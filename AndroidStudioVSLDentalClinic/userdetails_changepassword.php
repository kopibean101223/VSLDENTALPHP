<?php
require 'db_connect.php';   

$userid = $_POST['userid'] ?? null;
$password = $_POST['password'] ?? null;

try {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE accounts SET password = :password WHERE accounts_id = :userid";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); 
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
?>
