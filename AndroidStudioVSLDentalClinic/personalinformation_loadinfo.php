    <?php
header('Content-Type: application/json');
require 'db_connect.php';    // make sure $conn is PDO instance

$patientid = isset($_POST['patientid']) ? $_POST['patientid'] : null;

if ($patientid) {
    $query = "SELECT * FROM patients WHERE patient_id = :patientid";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':patientid', $patientid, PDO::PARAM_INT); // PDO binding
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
