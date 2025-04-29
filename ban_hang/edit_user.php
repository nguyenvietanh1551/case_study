<?php
session_start();

// Kiểm tra nếu chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "shop_db");

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Cập nhật thông tin người dùng
    $query = "UPDATE users SET username='$username', email='$email' WHERE id='$id'";
    
    if ($conn->query($query) === TRUE) {
        echo "Cập nhật thông tin thành công!";
        header("Location: users.php"); // Quay lại trang quản lý người dùng
        exit();
    } else {
        echo "Lỗi: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    // Lấy thông tin người dùng để hiển thị trong form
    $id = $_GET['id'];
    $conn = new mysqli("localhost", "root", "", "shop_db");
    $query = "SELECT * FROM users WHERE id='$id'";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Người Dùng</title>
</head>
<body>
    <h1>Sửa Thông Tin Người Dùng</h1>
    <form method="POST" action="edit_user.php">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        Tên người dùng: <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
