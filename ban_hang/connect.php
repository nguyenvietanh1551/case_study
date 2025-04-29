<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "shop_db"; // tên database mày tạo trong phpMyAdmin

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// echo "Kết nối thành công"; // bật dòng này để test thử nếu muốn
?>
