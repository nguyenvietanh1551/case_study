<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shop_db");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        echo "Mật khẩu xác nhận không khớp!";
    } else {
        $query = "UPDATE users SET password='$new_password' WHERE id='$user_id'";
        if ($conn->query($query) === TRUE) {
            echo "Đổi mật khẩu thành công!";
            header("Location: users.php");
            exit();
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
}
?>

<h2>Đổi mật khẩu người dùng</h2>
<form method="POST">
    Mật khẩu mới: <input type="password" name="new_password" required><br>
    Xác nhận mật khẩu: <input type="password" name="confirm_password" required><br>
    <button type="submit">Cập nhật mật khẩu</button>
</form>
