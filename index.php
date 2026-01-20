<?php
// index.php

// Load environment variables
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'testdb';
$dbUser = getenv('DB_USER') ?: 'postgres';
$dbPassword = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbStatus = "Connected to database successfully!";
} catch (PDOException $e) {
    $dbStatus = "Database connection failed: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Render PHP App</title>
</head>
<body>
    <h1>Hello, Render!</h1>
    <p><?php echo $dbStatus; ?></p>
</body>
</html>
