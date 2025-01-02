<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // 判斷使用者是否登入
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header("Location: login.php"); // 如果沒有登入，重定向到登入頁面
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>個人資訊 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // JavaScript 用來切換顯示的內容
        function showContent(section) {
            // 隱藏所有的區塊
            const sections = document.querySelectorAll('.content');
            sections.forEach(function (sec) {
                sec.style.display = 'none';
            });

            // 顯示選中的區塊
            const activeSection = document.getElementById(section);
            activeSection.style.display = 'block';

            // 更改按鈕樣式
            const buttons = document.querySelectorAll('.sidebar a');
            buttons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            // 高亮選中的按鈕
            document.getElementById('btn-' + section).classList.add('active');
        }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">
            <a href="index.php">CowPee購物</a>
        </div>
        <nav class="menu">
            <a href="cart.php">購物車</a>
            <a href="logout.php">登出</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main style="display: flex;">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 200px; padding: 20px; background-color: #f4f4f4; height: 100vh;">
            <h3>個人資訊</h3>
            <a href="javascript:void(0)" id="btn-account" onclick="showContent('account')">帳號資訊</a>
            <a href="javascript:void(0)" id="btn-coupons" onclick="showContent('coupons')">折價券管理</a>
            <a href="javascript:void(0)" id="btn-sold-products" onclick="showContent('sold-products')">販售商品</a>
            <a href="javascript:void(0)" id="btn-purchase-history" onclick="showContent('purchase-history')">購買紀錄</a>
        </div>

        <!-- Content Section -->
        <div class="content-section" style="flex-grow: 1; padding: 20px;">
        <section id="account" class="content">
            <h2>帳號資訊</h2>
            <div class="account-info">
                <p><strong>使用者名稱：</strong> <?= htmlspecialchars($username) ?></p>
                <p><strong>電子郵件：</strong> user@example.com</p> <!-- 可以從資料庫中提取資料 -->
            </div>
        </section>
        <section id="coupons" class="content" style="display: none;">
            <h2>折價券管理</h2>
            <p>目前擁有 3 張折價券</p>
            <div class="coupons-container">
                <div class="coupon-card">
                    <h3>10% 折扣</h3>
                    <p><strong>開始日期：</strong>2024/01/01</p>
                    <p><strong>結束日期：</strong>2024/12/31</p>
                    <p><strong>折扣幅度：</strong>10%</p>
                    <a href="cart.php" class="use-button">使用</a>
                </div>
                <div class="coupon-card">
                    <h3>50元優惠</h3>
                    <p><strong>開始日期：</strong>2024/03/01</p>
                    <p><strong>結束日期：</strong>2024/06/30</p>
                    <p><strong>折扣幅度：</strong>50元</p>
                    <a href="cart.php" class="use-button">使用</a>
                </div>
                <div class="coupon-card">
                    <h3>滿千折200元</h3>
                    <p><strong>開始日期：</strong>2024/04/01</p>
                    <p><strong>結束日期：</strong>2024/09/30</p>
                    <p><strong>折扣幅度：</strong>200元</p>
                    <a href="cart.php" class="use-button">使用</a>
                </div>
            </div>
        </section>


        <section id="sold-products" class="content" style="display: none;">
            <h2>販售商品</h2>
            <ul>
                <li>商品 1</li>
                <li>商品 2</li>
                <li>商品 3</li>
            </ul>
        </section>

        <section id="purchase-history" class="content" style="display: none;">
            <h2>購買紀錄</h2>
            <ul>
                <li>2024/12/20 - 產品 1 (NT$500)</li>
                <li>2024/12/15 - 產品 2 (NT$1200)</li>
                <li>2024/12/10 - 產品 3 (NT$800)</li>
            </ul>
        </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
