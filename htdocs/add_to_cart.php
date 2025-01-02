<?php
session_start();

if (isset($_GET['id'], $_GET['name'], $_GET['price'])) {
    $id = $_GET['id'];
    $name = $_GET['name'];
    $price = $_GET['price'];

    // 如果購物車還沒有，初始化它
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 如果商品已經在購物車裡，增加數量
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        // 否則，把商品添加到購物車
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => 1
        ];
    }
}

// 跳轉回購物車頁面
header("Location: cart.php");
exit();
?>
