<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "shop_db");

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Kiểm tra tên đăng nhập trong cơ sở dữ liệu
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // So sánh mật khẩu đã mã hóa với mật khẩu người dùng nhập
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: admin.php"); // Redirect đến trang quản lý sau khi đăng nhập thành công
            exit;
        } else {
            echo "Tên đăng nhập hoặc mật khẩu sai!";
        }
    } else {
        echo "Tên đăng nhập hoặc mật khẩu sai!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Đăng nhập</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
        </form>
        <p class="text-center mt-3">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    </div>
</body>
</html>
