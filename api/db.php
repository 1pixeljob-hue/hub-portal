<?php
// File cấu hình kết nối MySQL (PDO)

$host = 'localhost'; // Thường là localhost trên cPanel
$db = 'YOUR_DATABASE_NAME'; // Tên database bạn tạo trên cPanel
$user = 'YOUR_DATABASE_USER'; // Tên user database bạn tạo
$pass = 'YOUR_DATABASE_PASSWORD'; // Mật khẩu user database
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
