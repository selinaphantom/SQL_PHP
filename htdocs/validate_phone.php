<?php
session_start();
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
    $username = $_SESSION['username']; // 取得登入使用者的使用者名稱

    // 檢查手機號碼是否已經被註冊
    $stmt = $conn->prepare("SELECT COUNT(*) FROM member WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // 如果手機號碼已經存在
        echo "exists";
    } else {
        // 如果手機號碼有效，更新資料庫中的手機號碼
        $stmt = $conn->prepare("UPDATE member SET phone = ?, authority = ? WHERE username = ?");
        $authority = '賣家權限'; // 更新為賣家權限
        $stmt->bind_param("sss", $phone, $authority, $username);
        $stmt->execute();
        // 更新成功，返回個人資訊頁面
        echo "valid";
    }
}

$conn->close();
?>
