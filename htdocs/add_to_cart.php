<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userID = $isLoggedIn ? $_SESSION['user_id'] : null; // 假設 user_id 儲存在 session 中

// 若使用者未登入，重定向至登入頁面
if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 確認是否收到商品 ID 和名稱
if (isset($_GET['id'], $_GET['name'], $_GET['price'])) {
    $productId = $_GET['id'];
    $productName = $_GET['name'];
    $productPrice = $_GET['price'];

    // 查詢 list_items 表是否已經有該商品
    $stmt = $conn->prepare("SELECT quatity FROM list_items WHERE customer_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userID, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 商品已經在購物車中，增加數量
        $row = $result->fetch_assoc();
        $newQuantity = $row['quatity'] + 1;

        // 更新數量
        $updateStmt = $conn->prepare("UPDATE list_items SET quatity = ? WHERE customer_id = ? AND product_id = ?");
        $updateStmt->bind_param("iii", $newQuantity, $userID, $productId);
        $updateStmt->execute();
    } else {
        // 商品不在購物車中，插入新記錄
        $insertStmt = $conn->prepare("INSERT INTO list_items (customer_id, product_id, quatity) VALUES (?, ?, ?)");
        $quantity = 1; // 預設數量為 1
        $insertStmt->bind_param("iii", $userID, $productId, $quantity);
        $insertStmt->execute();
    }

    // 重定向回購物車頁面
    header("Location: cart.php");
    exit();
} else {
    // 如果沒有收到商品 ID，則重定向回主頁
    header("Location: index.php");
    exit();
}
