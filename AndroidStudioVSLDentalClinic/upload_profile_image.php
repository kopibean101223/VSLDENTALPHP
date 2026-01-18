<?php
header('Content-Type: application/json');

try {
    require 'db_connect.php';

    $accounts_id = $_POST['accounts_id'] ?? null;
    $imageBase64 = $_POST['image'] ?? null;

    if (!$accounts_id || !$imageBase64) {
        throw new Exception("Missing user ID or image data.");
    }

    // FIX #1 — Restore + signs (Base64 breaks without this)
    $imageBase64 = str_replace(' ', '+', $imageBase64);

    // FIX #2 — Decode safely
    $imageData = base64_decode($imageBase64, true);
    if ($imageData === false) {
        throw new Exception("Base64 decode failed – image corrupted.");
    }

    // Save file
    $fileName = time() . "_profile.jpg";
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    file_put_contents($uploadDir . $fileName, $imageData);

    // Save filename to DB
    $stmt = $conn->prepare("UPDATE accounts SET profilepic = :file WHERE accounts_id = :id");
    $stmt->execute([
        ':file' => $fileName,
        ':id' => $accounts_id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Profile image updated successfully.",
        "fileName" => $fileName
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
