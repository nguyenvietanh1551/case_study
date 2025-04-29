<?php
session_start();
include("connect.php");

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);

    if ($quantity > $product['quantity']) {
        echo "Số lượng sản phẩm không đủ!";
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
    }

    $new_quantity = $product['quantity'] - $quantity;
    $update_query = "UPDATE products SET quantity = $new_quantity WHERE id = $product_id";
    mysqli_query($conn, $update_query);

    header("Location: shop.php");
    exit;
}

// Xử lý thanh toán
if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "Giỏ hàng của bạn hiện tại trống!";
        exit;
    }

    $total = 0;
    foreach ($_SESSION['cart'] as $product) {
        $total += $product['price'] * $product['quantity'];
    }

    unset($_SESSION['cart']);

    echo "<h2>Thanh toán thành công!</h2>";
    echo "<p>Tổng số tiền bạn cần thanh toán: " . number_format($total) . " đ</p>";
    exit;
}

// Lấy danh sách sản phẩm
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop - Mua Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Nút Admin -->
<div class="container mt-3">
    <div class="d-flex justify-content-end">
        <a href="admin.php" class="btn btn-outline-dark">Quản trị (Admin)</a>
    </div>
</div>

<div class="container mt-4">
    <h2 class="text-center">Danh sách sản phẩm</h2>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="images/<?= $row['image']; ?>" class="card-img-top" alt="<?= $row['name']; ?>" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['name']; ?></h5>
                        <p class="card-text">Giá: <?= number_format($row['price']); ?> đ</p>
                        <p class="card-text">Số lượng còn lại: <?= $row['quantity']; ?></p>
                        <form action="shop.php" method="POST">
                            <div class="mb-2">
                                <input type="number" name="quantity" class="form-control" min="1" max="<?= $row['quantity']; ?>" value="1" required>
                            </div>
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Giỏ hàng -->
    <h3 class="mt-5">Giỏ hàng</h3>
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $product) {
                    $total += $product['price'] * $product['quantity'];
                ?>
                    <tr>
                        <td><?= $product['name']; ?></td>
                        <td><?= $product['quantity']; ?></td>
                        <td><?= number_format($product['price']); ?> đ</td>
                        <td><?= number_format($product['price'] * $product['quantity']); ?> đ</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <h4>Tổng tiền: <?= number_format($total); ?> đ</h4>
        <form action="shop.php" method="POST">
            <button type="submit" name="checkout" class="btn btn-success">Thanh toán</button>
        </form>
    <?php } else { ?>
        <p>Giỏ hàng của bạn hiện tại trống!</p>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
