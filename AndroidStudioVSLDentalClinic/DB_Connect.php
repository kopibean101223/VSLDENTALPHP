<?php
// db_connect.php

$host = "localhost";         // usually localhost
$port = "5432";              // default PostgreSQL port
$dbname = "VSLDENTALCLINIC";   // your database name
$user = "postgres";          // your PostgreSQL username
$password = "tarog292004"; // change to your PostgreSQL password

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>