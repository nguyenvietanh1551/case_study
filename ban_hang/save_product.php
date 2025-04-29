<?php
include("connect.php");

// Lấy dữ liệu từ form
$id    = $_POST['id'];
$name  = $_POST['name'];
$price = $_POST['price'];

// Xử lý hình ảnh
$image = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "images/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    // Upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image = $image_name;
    }
}

// Nếu có ID => Sửa
if (!empty($id)) {
    if (!empty($image)) {
        $sql = "UPDATE products SET name='$name', price='$price', image='$image' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', price='$price' WHERE id=$id";
    }
} else {
    // Thêm mới
    $sql = "INSERT INTO products (name, price, image, created_at, quantity) 
        VALUES ('$name', '$price', '$image', NOW(), 0)";

}

if (mysqli_query($conn, $sql)) {
    header("Location: admin.php");
    exit;
} else {
    echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
}
?>
