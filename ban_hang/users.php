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

$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">👤 Quản lý người dùng</h2>

    <div class="mb-3">
        <a href="add_user.php" class="btn btn-success">➕ Thêm người dùng</a>
        <a href="admin.php" class="btn btn-primary">🏠 Dashboard</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa người dùng này?')">Xóa</a>
                    <a href="change_password.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Đổi mật khẩu</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
