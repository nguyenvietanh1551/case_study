<?php
include("connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xoá sản phẩm
    $sql = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php"); // Quay về lại trang admin
        exit;
    } else {
        echo "Lỗi khi xoá sản phẩm: " . mysqli_error($conn);
    }
} else {
    echo "Không có ID sản phẩm để xoá!";
}
?>
