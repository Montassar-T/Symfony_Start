<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=symfoDb;charset=utf8mb4', 'root', '20202526');
    echo "PDO MySQL connection successful!";
} catch (PDOException $e) {
    echo "PDO MySQL connection failed: " . $e->getMessage();
}
?>
