<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 模擬帳號驗證
    if ($username === 'admin' && $password === '1234') {
        $_SESSION['username'] = $username; // 儲存登入狀態
        header('Location: index.php'); // 登入成功返回主畫面
        exit();
    } else {
        echo "<script>
                alert('登入失敗，請檢查帳號和密碼是否正確！');
                window.location.href = 'login.php';
              </script>";
    }
} else {
    header('Location: login.php');
    exit();
}
?>
