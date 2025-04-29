<?php
session_start();

// Kiểm tra nếu chưa đăng nhập, redirect về trang login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

include("connect.php");
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-4 text-center">Trang quản lý sản phẩm</h2>

    <!-- Nút Quay lại trang người dùng -->
    <a href="users.php" class="btn btn-info mb-3">Quản lý người dùng</a>

    <a href="shop.php" class="btn btn-info mb-3">Quản lý Shop bán hàng</a>

    <!-- Nút Đăng xuất -->
    <a href="admin.php?logout=true" class="btn btn-danger mb-3">Đăng xuất</a>

    <!-- Thêm sản phẩm -->
    <button class="btn btn-success mb-3" onclick="showAddForm()">+ Thêm sản phẩm</button>
    
    <!-- Tìm kiếm -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm sản phẩm...">
    </div>


    <!-- Danh sách sản phẩm -->
    <table class="table table-bordered" id="productTable">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Số lượng</th> <!-- Cột Số lượng -->
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= number_format($row['price']); ?> đ</td>
                    <td><?= $row['quantity']; ?> sản phẩm</td> <!-- Hiển thị số lượng sản phẩm -->
                    <td><img src="images/<?= $row['image']; ?>" width="50"></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editProduct(<?= htmlspecialchars(json_encode($row)); ?>)">Sửa</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $row['id']; ?>)">Xóa</button>
                        <button class="btn btn-primary btn-sm" onclick="showAddQuantityForm(<?= $row['id']; ?>)">Thêm Số Lượng</button> <!-- Nút thêm số lượng -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Form thêm/sửa -->
<div id="formBox" class="container mb-4" style="display:none;">
    <h4 id="formTitle">Thêm sản phẩm</h4>
    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="productId">
        <div class="mb-2">
            <input type="text" name="name" id="productName" class="form-control" placeholder="Tên sản phẩm" required>
        </div>
        <div class="mb-2">
            <input type="number" name="price" id="productPrice" class="form-control" placeholder="Giá" required>
        </div>
        <div class="mb-2">
            <input type="number" name="quantity" id="productQuantity" class="form-control" placeholder="Số lượng sản phẩm" required>
        </div>
        <div class="mb-2">
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Lưu</button>
        <button type="button" onclick="hideForm()" class="btn btn-secondary">Hủy</button>
    </form>
</div>

<!-- Form thêm số lượng sản phẩm -->
<div id="addQuantityForm" class="container mb-4" style="display:none;">
    <h4>Thêm số lượng sản phẩm</h4>
    <form action="update_quantity.php" method="POST">
        <input type="hidden" name="product_id" id="productIdForQuantity">
        <div class="mb-2">
            <input type="number" name="quantity" id="newQuantity" class="form-control" placeholder="Nhập số lượng muốn thêm" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật số lượng</button>
        <button type="button" onclick="hideQuantityForm()" class="btn btn-secondary">Hủy</button>
    </form>
</div>

<script>
// Tìm kiếm
document.getElementById("searchInput").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#productTable tbody tr");
    rows.forEach(row => {
        let name = row.children[1].textContent.toLowerCase();
        row.style.display = name.includes(keyword) ? "" : "none";
    });
});

// Hiện form thêm mới
function showAddForm() {
    document.getElementById("formBox").style.display = "block";
    document.getElementById("formTitle").innerText = "Thêm sản phẩm";
    document.getElementById("productId").value = "";
    document.getElementById("productName").value = "";
    document.getElementById("productPrice").value = "";
    document.getElementById("productQuantity").value = "";
}

// Sửa
function editProduct(product) {
    showAddForm();
    document.getElementById("formTitle").innerText = "Sửa sản phẩm";
    document.getElementById("productId").value = product.id;
    document.getElementById("productName").value = product.name;
    document.getElementById("productPrice").value = product.price;
    document.getElementById("productQuantity").value = product.quantity;
}

// Ẩn form thêm sản phẩm
function hideForm() {
    document.getElementById("formBox").style.display = "none";
}

// Xoá sản phẩm
function deleteProduct(id) {
    if (confirm("Bạn có chắc chắn muốn xoá sản phẩm này không?")) {
        window.location.href = "delete_product.php?id=" + id;
    }
}

// Hiện form thêm số lượng
function showAddQuantityForm(productId) {
    document.getElementById("addQuantityForm").style.display = "block";
    document.getElementById("productIdForQuantity").value = productId;
}

// Ẩn form thêm số lượng
function hideQuantityForm() {
    document.getElementById("addQuantityForm").style.display = "none";
}
</script>

</body>
</html>
