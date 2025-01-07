<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 查詢重複最多的前三個 Category_id
$sql = "SELECT Category_id, COUNT(*) AS count FROM product GROUP BY Category_id ORDER BY count DESC LIMIT 3";
$result = $conn->query($sql);
$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['Category_id'];
    }
}

// 取得每個類別的第一個商品並顯示
$productItems = [];
foreach ($categories as $categoryId) {
    $stmt = $conn->prepare("SELECT * FROM product WHERE Category_id = ? ORDER BY product_id LIMIT 1");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $productResult = $stmt->get_result();
    $product = $productResult->fetch_assoc();
    
    if ($product) {
        // 根據 product_id 查詢對應的圖片
        $stmt = $conn->prepare("SELECT image_path FROM image_product WHERE ID = ?");
        $stmt->bind_param("i", $product['product_id']);
        $stmt->execute();
        $imageResult = $stmt->get_result();
        $image = $imageResult->fetch_assoc();
        
        $productItems[] = [
            'product' => $product,
            'image' => $image ? $image['image_path'] : 'img/default.jpg'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CowPee購物 - 主畫面</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Banner Section */
        .banner img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Products Section */
        .products {
            padding: 40px 0;
            background-color: #f9f9f9;
        }

        .products h2 {
            font-size: 32px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            color:rgb(9, 9, 9);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            justify-items: center;
            margin-top: 20px;
        }
        .product-item {
            display: flex;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease-in-out;
            width: 100%;
            max-width: 300px;
            justify-content: center; /* 水平居中 */
            align-items: center;     /* 垂直居中 */
            flex-direction: column;  /* 垂直排列 */
        }

        .product-item:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .product-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-item:hover img {
            transform: scale(1.1);
        }

        .product-item h3 {
            margin: 15px 0;
            font-size: 18px;
            font-weight: 500;
        }
        .product-item img {
            width: 250px;  /* 設定固定寬度 */
            height: 250px; /* 設定固定高度 */
            object-fit: cover; /* 保持圖片比例並裁切 */
            border-radius: 10px; /* 圓角效果 */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* 輕微陰影效果 */
        }

        .product-item p {
            color: #888;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .product-item button {
            background-color: #ff6f61;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-item button:hover {
            background-color: #ff5733;
        }

        .product-item .price {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
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
                <a href="cart.php">購物車</a>
                <div class="user-info">
                    <span><a href="profile.php">歡迎 <?= htmlspecialchars($username) ?></a></span>
                    <a href="logout.php">登出</a>
                </div>
            <?php else: ?>
                <a href="login.php">登入</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Banner Section -->
        <section class="banner">
            <img src="img/長圖片.jpg" alt="蝦皮主頁橫幅" onerror="this.src='img/CowPee.jpeg'">
        </section>
        <!-- Product Grid -->
        <section class="products">
            <h2>熱門商品</h2>
            <div class="product-grid">
                <?php foreach ($productItems as $item): ?>
                    <div class="product-item">
                        <!-- 商品圖片 -->
                        <a href="product_detail.php?product_id=<?= $item['product']['product_id'] ?>">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="產品圖片">
                        </a>
                        <!-- 商品名稱 -->
                        <h3><a href="product_detail.php?product_id=<?= $item['product']['product_id'] ?>"><?= htmlspecialchars($item['product']['Product_name']) ?></a></h3>
                        <!-- 商品簡短描述 -->
                        <p><?= htmlspecialchars(substr($item['product']['Description'], 0, 60)) ?>...</p>
                        <!-- 商品價格 -->
                        <p class="price">NT$<?= htmlspecialchars($item['product']['Price']) ?></p>
                        <!-- 加入購物車按鈕 -->
                        <a href="add_to_cart.php?id=<?= $item['product']['product_id'] ?>&name=<?= urlencode($item['product']['Product_name']) ?>&price=<?= $item['product']['Price'] ?>"><button>加入購物車</button></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
