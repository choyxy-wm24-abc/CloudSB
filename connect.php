<?php
// Set PHP default timezone globally
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Asia/Kuala_Lumpur');

try {
    $_db = new PDO(
        "mysql:host=dbinstance.c0hv042dsohi.us-east-1.rds.amazonaws.com;port=3306;dbname=dbinstance;charset=utf8mb4",
        "admin",
        "food1234",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,   // fetch as objects
            PDO::ATTR_EMULATE_PREPARES => false               // use real prepared statements
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
