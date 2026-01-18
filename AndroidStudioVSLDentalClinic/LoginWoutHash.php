<?php
require 'db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email or password missing'
        ]);
        exit;
    }

    try {

        $sql = "
            SELECT 
                a.accounts_id,
                a.email,
                a.password,
                a.status,
                p.patient_id,
                s.staff_id,
                d.dentist_id
            FROM accounts a
            LEFT JOIN patients p ON p.accounts_id = a.accounts_id
            LEFT JOIN staffs s ON s.accounts_id = a.accounts_id
            LEFT JOIN dentists d ON d.accounts_id = a.accounts_id
            WHERE a.email = :email
              AND a.status = 'Active'
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password== $user['password']) {

            // Determine role
            if (!empty($user['staff_id'])) {
                $role = "Staff";
                $role_id = $user['staff_id'];
            } elseif (!empty($user['patient_id'])) {
                $role = "Patient";
                $role_id = $user['patient_id'];
            } elseif (!empty($user['dentist_id'])) {
                $role = "Doctor";
                $role_id = $user['dentist_id'];
            }
            else {
                $role = "Unknown";
                $role_id = null;
            }

            // Create session
            $_SESSION['user_id'] = $user['accounts_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $role;
            $_SESSION['role_id'] = $role_id;

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "role" => $role,
                "user_id" => $user['accounts_id'],
                "role_id" => $role_id,
                "session_id" => session_id()
            ]);

        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password"
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}
?>
