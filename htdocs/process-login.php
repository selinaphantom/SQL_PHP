<?php
// 資料庫連線設定
$servername = "localhost";
$db_username = "root"; // 根據你的 XAMPP 預設
$db_password = ""; // 預設為空
$dbname = "cowpee"; // 資料庫名稱

// 建立連線
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 檢查表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 防止 SQL Injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // 查詢帳號和密碼
    $sql = "SELECT * FROM member WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 登入成功，儲存使用者資訊到 Session
        session_start();
        $_SESSION['username'] = $username;
        header("Location: index.php"); // 導向主畫面
        exit();
    } else {
        // 登入失敗，回傳錯誤訊息
        header("Location: login.php?error=帳號或密碼錯誤");
        exit();
    }
}

// 關閉連線
$conn->close();
?>
