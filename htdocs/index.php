<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // 判斷使用者是否登入
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CowPee購物 - 主畫面</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 產品名稱顏色 */
        .products h3 {
            color: black;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">
            <a href="index.php">CowPee購物</a>
        </div>
        <form action="search.php" method="GET" class="search-bar">
            <input type="text" name="query" placeholder="搜尋商品或店家">
            <button type="submit">搜尋</button>
        </form>
        <nav class="menu">
            <a href="#">分類</a>
            <?php if ($isLoggedIn): ?>
                <a href="cart.php">購物車</a> <!-- 購物車連結 -->
                <div class="user-info">
                    <span><a href="profile.php">歡迎 <?= htmlspecialchars($username) ?></a></span> <!-- 用戶名稱點擊進入個人資料頁面 -->
                    <a href="logout.php">登出</a>
                </div>
            <?php else: ?>
                <a href="login.php">登入</a> <!-- 若未登入則顯示登入 -->
            <?php endif; ?>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Banner Section -->
        <section class="banner">
            <img src="img/長圖片.jpg" alt="蝦皮主頁橫幅" onerror="this.src='img/CowPee.jpeg'">
        </section>

        <!-- Product Section -->
        <section class="products">
            <h2>熱門商品</h2>
            <div class="product-grid">
                <div class="product-item">
                    <img src="product1.jpg" alt="產品 1" onerror="this.src='img/CowPee.jpeg'">
                    <h3>產品名稱 1</h3>
                    <p>價格：NT$500</p>
                    <a href="add_to_cart.php?id=1&name=產品名稱1&price=500"><button>加入購物車</button></a>
                </div>
                <div class="product-item">
                    <img src="product2.jpg" alt="產品 2" onerror="this.src='img/CowPee.jpeg'">
                    <h3>產品名稱 2</h3>
                    <p>價格：NT$1200</p>
                    <a href="add_to_cart.php?id=2&name=產品名稱2&price=1200"><button>加入購物車</button></a>
                </div>
                <div class="product-item">
                    <img src="product3.jpg" alt="產品 3" onerror="this.src='img/CowPee.jpeg'">
                    <h3>產品名稱 3</h3>
                    <p>價格：NT$800</p>
                    <a href="add_to_cart.php?id=3&name=產品名稱3&price=800"><button>加入購物車</button></a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
