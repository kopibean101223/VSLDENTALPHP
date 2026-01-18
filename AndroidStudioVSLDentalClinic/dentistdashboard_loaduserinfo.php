    <?php
header('Content-Type: application/json');
require 'db_connect.php';    // make sure $conn is PDO instance

$roleid = isset($_POST['roleid']) ? $_POST['roleid'] : null;

if ($roleid) {
    $query = "SELECT * FROM dentists WHERE dentist_id = :roleid";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':roleid', $roleid, PDO::PARAM_INT); // PDO binding
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "userID missing"
    ]);
}
?>
