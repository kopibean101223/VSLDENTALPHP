<?php
// db_connect.php

$host = "dpg-d5lmiqcmrvns73ejor6g-a";         // usually localhost
$port = "5432";              // default PostgreSQL port
$dbname = "vsldentalclinic";   // your database name
$user = "vsldentalclinic_user";          // your PostgreSQL username
$password = "ooro8vftuv4NViuMIaDhjolP7gldgAoh"; // change to your PostgreSQL password

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>