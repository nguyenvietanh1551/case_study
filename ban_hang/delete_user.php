<?php
session_start();

// Kiểm tra nếu chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    // Lấy ID người dùng cần xóa
    $id = $_GET['id'];

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "shop_db");

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Xóa người dùng khỏi cơ sở dữ liệu
    $query = "DELETE FROM users WHERE id='$id'";

    if ($conn->query($query) === TRUE) {
        echo "Xóa người dùng thành công!";
        header("Location: users.php"); // Quay lại trang quản lý người dùng
        exit();
    } else {
        echo "Lỗi: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
