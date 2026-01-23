<?php
// db_connect.php

$databaseUrl = getenv('postgresql://vsldentalclinic_user:ooro8vftuv4NViuMIaDhjolP7gldgAoh@dpg-d5lmiqcmrvns73ejor6g-a.oregon-postgres.render.com:5432/vsldentalclinic?sslmode=require');

if (!$databaseUrl) {
    error_log("DATABASE_URL not set");
    http_response_code(500);
    exit;
}

$db = parse_url($databaseUrl);

try {
    $conn = new PDO(
        "pgsql:host={$db['host']};port={$db['port']};dbname=" . ltrim($db['path'], '/'),
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    http_response_code(500);
    exit;
}

