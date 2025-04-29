<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "shop_db");

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $check_username = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_username);

    if ($result->num_rows > 0) {
        echo "Tên đăng nhập đã tồn tại!";
    } else {
        // Lưu thông tin người dùng vào database
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        if ($conn->query($query) === TRUE) {
            echo "Đăng ký thành công! <a href='login.php'>Đăng nhập</a>";
        } else {
            echo "Có lỗi khi đăng ký: " . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Đăng ký tài khoản</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
        </form>
        <p class="text-center mt-3">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>
</html>
