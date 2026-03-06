<?php
// File cấu hình kết nối MySQL (PDO)

$host = 'localhost'; // Thường là localhost trên cPanel
$db = 'gtxjozdehosting_hubportal'; // Tên database bạn tạo trên cPanel
$user = 'gtxjozdehosting_hubportal'; // Tên user database bạn tạo
$pass = 'Spencil@123'; // Mật khẩu user database
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
    // Trong môi trường production, bạn nên log lỗi thay vì hiển thị trực tiếp
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>
