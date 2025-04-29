<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Thêm sản phẩm vào giỏ
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $_SESSION['cart'][] = $product_id;
}

// Hiển thị giỏ hàng
echo "<h2>Giỏ hàng</h2>";
foreach ($_SESSION['cart'] as $product_id) {
    $conn = new mysqli("localhost", "root", "", "shop_db");
    $query = "SELECT * FROM products WHERE id=$product_id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    echo "<p>{$row['name']} - " . number_format($row['price'], 0, ',', '.') . " VND</p>";
}

echo "<br><a href='checkout.php'>Thanh toán</a>";
?>
