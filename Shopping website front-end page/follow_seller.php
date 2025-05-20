<?php
session_start();

// 確認用戶是否已登入
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('請先登入！'); window.history.back();</script>";
    exit();
}

// 取得用戶 ID 和賣家 ID
$userID = $_SESSION['user_id'];
$sellerID = isset($_POST['seller_id']) ? intval($_POST['seller_id']) : 0;

if ($sellerID <= 0) {
    echo "<script>alert('無效的賣家 ID！'); window.history.back();</script>";
    exit();
}

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    echo "<script>alert('資料庫連接失敗！'); window.history.back();</script>";
    exit();
}

// 檢查是否已經追蹤該賣家
$stmt = $conn->prepare("SELECT COUNT(*) FROM list_liked WHERE customer_id = ? AND seller_id = ?");
$stmt->bind_param("ii", $userID, $sellerID);
$stmt->execute();
$result = $stmt->get_result();
$isFollowing = $result->fetch_row()[0] > 0;

// 設定操作結果訊息
$message = '';

if ($isFollowing) {
    // 已追蹤 -> 執行取消追蹤
    $stmt = $conn->prepare("DELETE FROM list_liked WHERE customer_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $userID, $sellerID);
    if ($stmt->execute()) {
        $message = "成功取消追蹤賣家！";
    } else {
        $message = "取消追蹤失敗：" . $conn->error;
    }
} else {
    // 未追蹤 -> 執行新增追蹤
    $stmt = $conn->prepare("INSERT INTO list_liked (customer_id, seller_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userID, $sellerID);
    if ($stmt->execute()) {
        $message = "成功追蹤賣家！";
    } else {
        $message = "追蹤失敗：" . $conn->error;
    }
}

$stmt->close();
$conn->close();

// 使用 JavaScript 彈出訊息並返回上一頁
echo "<script>
    alert('$message');
    window.history.back();
</script>";
exit();
?>
