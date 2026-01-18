<?php
require 'db_connect.php'; // your PDO connection
header('Content-Type: application/json');

try {
    // Get POST input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'Doctor';
    $role = "Doctor";
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $profilepic = "default.jpg";

    // Basic validation
    if (empty($email) || empty($password) || empty($firstname) || empty($lastname)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Start transaction
    $conn->beginTransaction();

    // Insert into accounts
    $sqlAccount = "INSERT INTO accounts (email, password, role, status,profilepic) 
                   VALUES (:email, :password, :role, 'Active', :profilepic) RETURNING accounts_id";
    $stmt = $conn->prepare($sqlAccount);
    $stmt->execute([
        ':email' => $email,
        ':password' => $hashedPassword,
        ':role' => $role,
        ':profilepic' => $profilepic
    ]);
    $accountId = $stmt->fetchColumn();

    // Insert into patients
    $sqlPatient = "INSERT INTO dentists (accounts_id, firstname, lastname, birthday, gender) 
                   VALUES (:accounts_id, :firstname, :lastname, NULL, NULL)";
    $stmt = $conn->prepare($sqlPatient);
    $stmt->execute([
        ':accounts_id' => $accountId,
        ':firstname' => $firstname,
        ':lastname' => $lastname
    ]);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Patient registered successfully",
        "account_id" => $accountId
    ]);

} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Registration Error: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
