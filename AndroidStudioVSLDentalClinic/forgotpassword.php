<?php
require 'db_connect.php';   

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
    ]);
    exit;
}

try {

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE accounts SET password = :password WHERE email = :email";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); 
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Password updated successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
