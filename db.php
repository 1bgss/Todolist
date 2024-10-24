<?php
$host = 'localhost'; 
$dbname = 'todo_list_db'; 
$username = 'root'; 
$password = ''; 

try {
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $koneksi->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
