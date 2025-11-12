<?php
// Cấu hình kết nối database
// Hỗ trợ cả PostgreSQL (Replit/Neon) và MySQL (XAMPP)

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Môi trường Replit/Production - Sử dụng PostgreSQL
    $db_parts = parse_url($database_url);
    
    $servername = $db_parts['host'];
    $dbname = ltrim($db_parts['path'], '/');
    $username = $db_parts['user'];
    $password = $db_parts['pass'];
    $port = $db_parts['port'] ?? 5432;
    
    try {
        $db_conn = new PDO("pgsql:host=$servername;port=$port;dbname=$dbname", $username, $password);
        $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
    }
} else {
    // Môi trường XAMPP - Sử dụng MySQL
    $servername = "localhost";
    $dbname = "sneakerdb2";
    $username = "root";
    $password = "";
    $port = "3306";
    
    try {
        $db_conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
        $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch(PDOException $e) {
        die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
    }
}
?>
