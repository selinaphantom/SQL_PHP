<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// 取得搜尋關鍵字
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 查詢符合搜尋關鍵字的商品
$sql = "SELECT * FROM product p LEFT JOIN image_product i ON p.product_id = i.ID WHERE p.Product_name LIKE ?;";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%"; // 使用 LIKE 搜尋商品名稱
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// 將搜尋結果存入陣列
$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
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
    <main>
        <section class="search-results">
            <h1>搜尋結果：<?= htmlspecialchars($query) ?></h1>
            <?php if (empty($results)): ?>
                <p>很抱歉，沒有找到符合的商品。</p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($results as $product): ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['Product_name']) ?>">
                            <h3><?= htmlspecialchars($product['Product_name']) ?></h3>
                            <p>價格：NT$<?= number_format($product['Price'], 0) ?></p>
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
