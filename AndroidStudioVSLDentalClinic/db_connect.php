<?php
// db_connect.php

$databaseUrl = getenv('DATABASE_URL');

// If Render's env var isn't found, use the hardcoded string
if (!$databaseUrl) {
    $databaseUrl = "postgresql://vsldentalclinic_user:ooro8vftuv4NViuMIaDhjolP7gldgAoh@dpg-d5lmiqcmrvns73ejor6g-a.oregon-postgres.render.com:5432/vsldentalclinic?sslmode=require";
}

try {
    // Convert the postgres:// protocol to the pgsql: prefix for PDO
    $dsn = str_replace("postgresql://", "pgsql:", $databaseUrl);
    
    // Split user/pass from the DSN if necessary, or just pass them if PDO handles it
    // Most modern PDO versions handle the full URL if formatted as:
    // pgsql:host=...;port=...;dbname=...;user=...;password=...;sslmode=require
    
    $db = parse_url($databaseUrl);
    $formattedDsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s;sslmode=require",
        $db['host'],
        $db['port'],
        ltrim($db['path'], '/'),
        $db['user'],
        $db['pass']
    );

    $conn = new PDO($formattedDsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    http_response_code(500);
    exit("Internal Server Error");
}
