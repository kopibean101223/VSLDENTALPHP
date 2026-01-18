<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    // Input
    $accountsid = isset($_POST['accountsid']) ? trim($_POST['accountsid']) : '';
    $filter = isset($_POST['filter']) ? trim($_POST['filter']) : 'all';

    if (empty($accountsid)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing accountsid"
        ]);
        exit;
    }

    // Base SQL
    $sql = "SELECT * FROM notifications WHERE accounts_id = :accountsid";

    // Filter conditions
    $params = [':accountsid' => $accountsid];

    if ($filter === 'today') {
    $sql .= " AND created_at::date = CURRENT_DATE";
} elseif ($filter === 'thisweek') {
    $sql .= " AND date_trunc('week', created_at) = date_trunc('week', CURRENT_DATE)";
} elseif ($filter === 'earlier') {
    $sql .= " AND created_at < (CURRENT_DATE - interval '1 month')";
}   

    $sql .= " ORDER BY created_at DESC";

    // Prepare and execute
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

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
