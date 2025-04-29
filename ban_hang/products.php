<?php
$conn = new mysqli("localhost", "root", "", "shop_db");

$query = "SELECT * FROM products";
$result = $conn->query($query);
?>
<h2>Sản phẩm</h2>
<div class="products">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product">
            <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="100">
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Giá: <?php echo number_format($row['price'], 0, ',', '.'); ?> VND</p>
            <a href="add_to_cart.php?id=<?php echo $row['id']; ?>">Thêm vào giỏ</a>
        </div>
    <?php endwhile; ?>
</div>
