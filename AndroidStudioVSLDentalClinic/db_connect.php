<?php
// db_connect.php

// 1. Get the environment variable by its NAME
$databaseUrl = getenv('DATABASE_URL');

// 2. If you are testing locally and DATABASE_URL isn't set, 
// you can provide a fallback (but don't commit secrets to GitHub!)
if (!$databaseUrl) {
    $databaseUrl = 'postgresql://vsldentalclinic_user:ooro8vftuv4NViuMIaDhjolP7gldgAoh@dpg-d5lmiqcmrvns73ejor6g-a.oregon-postgres.render.com:5432/vsldentalclinic?sslmode=require';
}

$db = parse_url($databaseUrl);

try {
    // Note: Render's managed PostgreSQL requires SSL
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname=" . ltrim($db['path'], '/');
    
    $conn = new PDO(
        $dsn,
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Force SSL if required by Render
            PDO::MYSQL_ATTR_SSL_CA => true 
        ]
    );
} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    http_response_code(500);
    echo "Connection Error"; // For debugging; remove in production
    exit;
}

