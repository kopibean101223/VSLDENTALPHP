    <?php
header('Content-Type: application/json');
require 'db_connect.php';    // make sure $conn is PDO instance

$userID = isset($_POST['userID']) ? $_POST['userID'] : null;

if ($userID) {
    $query = "SELECT * FROM accounts WHERE accounts_id = :userID";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':userID', $userID, PDO::PARAM_INT); // PDO binding
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
