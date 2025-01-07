<?php
session_start();
session_destroy(); // 清除所有 Session 資料
header('Location: index.php'); // 返回主畫面
exit();
?>
