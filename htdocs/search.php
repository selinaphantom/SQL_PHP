<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';

$products = [
    ['name' => '產品名稱 1', 'price' => 'NT$500', 'image' => 'images/product1.jpg'],
    ['name' => '產品名稱 2', 'price' => 'NT$1200', 'image' => 'images/product2.jpg'],
    ['name' => '產品名稱 3', 'price' => 'NT$800', 'image' => 'images/product3.jpg'],
];

// 篩選符合搜尋結果的商品
$results = array_filter($products, function ($product) use ($query) {
    return stripos($product['name'], $query) !== false;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>搜尋結果 - <?= htmlspecialchars($query) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">
            <a href="index.php">CowPee購物</a>
        </div>
        <form action="search.php" method="GET" class="search-bar">
            <input type="text" name="query" placeholder="搜尋商品或店家" value="<?= htmlspecialchars($query) ?>">
            <button type="submit">搜尋</button>
        </form>
        <nav class="menu">
            <a href="#">分類</a>
            <a href="#">購物車</a>
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <span>歡迎, <?= htmlspecialchars($username) ?></span>
                    <a href="logout.php">登出</a>
                </div>
            <?php else: ?>
                <a href="login.php">登入</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <section class="search-results">
            <h1>搜尋結果：<?= htmlspecialchars($query) ?></h1>
            <?php if (empty($results)): ?>
                <p>很抱歉，沒有找到符合的商品。</p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($results as $product): ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p>價格：<?= htmlspecialchars($product['price']) ?></p>
                            <button>加入購物車</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
