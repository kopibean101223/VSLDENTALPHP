<?php
require 'db_connect.php';   

$userid = $_POST['userid'] ?? null;
$firstName = $_POST['firstName'] ?? null;
$lastName = $_POST['lastName'] ?? null;
$birthDay = $_POST['birthDay'] ?? null;
$gender = $_POST['gender'] ?? null;

try {
    if (empty($userid)) {
        throw new Exception("Missing patient ID");
    }

    $fields = [];
    $params = [':userid' => $userid];

    if (!empty($firstName)) {
        $fields[] = "firstname = :firstName";
        $params[':firstName'] = $firstName;
    }

    if (!empty($lastName)) {
        $fields[] = "lastname = :lastName";
        $params[':lastName'] = $lastName;
    }

    if (!empty($gender)) {
        $fields[] = "gender = :gender";
        $params[':gender'] = $gender;
    }

    if (!empty($birthDay)) {
        $fields[] = "birthday = :birthDay";
        $params[':birthDay'] = $birthDay;
    }

    if (empty($fields)) {
        throw new Exception("No fields to update");
    }

    $sql = "UPDATE staffs SET " . implode(", ", $fields) . " WHERE staff_id = :userid";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'status' => 'success',
        'message' => 'Patient info updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
