<?php
// db_connect.php

$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    // Fallback for local testing
    $databaseUrl = 'postgresql://vsldentalclinic_user:ooro8vftuv4NViuMIaDhjolP7gldgAoh@dpg-d5lmiqcmrvns73ejor6g-a.oregon-postgres.render.com:5432/vsldentalclinic?sslmode=require';
}

$db = parse_url($databaseUrl);

try {
    // The sslmode=require in the connection string handles the security setup
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname=" . ltrim($db['path'], '/');
    
    // Check if sslmode is present in the query string and append it if not already there
    if (isset($db['query'])) {
        $dsn .= ";" . str_replace('&', ';', $db['query']);
    }

    $conn = new PDO(
        $dsn,
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
    // Be careful with echo $e->getMessage() in production as it reveals your host
    echo "Connection Error. Check logs."; 
    exit;
}

