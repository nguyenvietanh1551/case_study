<?php
session_start();
if (empty($_SESSION['cart'])) {
    echo "Giỏ hàng của bạn trống!";
    exit;
}

// Tính tổng giá trị đơn hàng
$total_price = 0;
foreach ($_SESSION['cart'] as $product_id) {
    $conn = new mysqli("localhost", "root", "", "shop_db");
    $query = "SELECT * FROM products WHERE id=$product_id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $total_price += $row['price'];
}

echo "Tổng giá trị đơn hàng: " . number_format($total_price, 0, ',', '.') . " VND<br>";
?>

<form method="POST" action="order.php">
    <button type="submit">Thanh toán</button>
</form>
