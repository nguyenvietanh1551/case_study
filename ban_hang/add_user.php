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

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kiểm tra trùng username
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $message = "⚠️ Tên đăng nhập đã tồn tại!";
    } else {
        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($query)) {
            $message = "✅ Thêm người dùng thành công!";
        } else {
            $message = "❌ Lỗi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>➕ Thêm người dùng mới</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3" onsubmit="return validateForm()">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="users.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script>
function validateForm() {
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (username.length < 3) {
        alert("Tên đăng nhập phải từ 3 ký tự trở lên!");
        return false;
    }
    if (!email.includes("@") || !email.includes(".")) {
        alert("Email không hợp lệ!");
        return false;
    }
    if (password.length < 6) {
        alert("Mật khẩu phải từ 6 ký tự trở lên!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
