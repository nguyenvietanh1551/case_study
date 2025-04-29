<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    // Cập nhật số lượng sản phẩm trong cơ sở dữ liệu
    $query = "UPDATE products SET quantity = quantity + $new_quantity WHERE id = $product_id";
    if (mysqli_query($conn, $query)) {
        echo "Số lượng sản phẩm đã được cập nhật!";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
    // Quay lại trang quản lý sản phẩm sau khi cập nhật
    header("Location: admin.php");
}
?>
