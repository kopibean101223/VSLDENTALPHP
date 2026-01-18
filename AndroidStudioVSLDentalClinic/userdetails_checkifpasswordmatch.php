<?php
header('Content-Type: application/json');
require 'db_connect.php';    // make sure $conn is PDO instance

$accountsid = $_POST['accountsid'] ?? null;
$enteredPassword = $_POST['password'] ?? null; // password sent from Android

if (!$accountsid || !$enteredPassword) {
    echo json_encode([
        "status" => "error",
        "message" => "userID or password missing"
    ]);
    exit;
}

try {
    $query = "SELECT * FROM accounts WHERE accounts_id = :accountsid";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':accountsid', $accountsid, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
        exit;
    }

    $hashedPassword = $row['password'];

    if (password_verify($enteredPassword, $hashedPassword)) {
        // Password matches
        echo json_encode([
            "status" => "success",
            "message" => "Password verified",
            "data" => $row
        ]);
    } else {
        // Password does not match
        echo json_encode([
            "status" => "error",
            "message" => "Incorrect password"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
