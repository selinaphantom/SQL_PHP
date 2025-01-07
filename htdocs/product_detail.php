<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 如果用戶已登入且沒有存 user_id，則從資料庫中查詢
if ($isLoggedIn && !isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT ID FROM member WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        $_SESSION['user_id'] = $user['ID'];
    } else {
        // 如果找不到用戶，處理錯誤
        echo "用戶資料錯誤，請重新登入。";
        exit();
    }
}

// 獲取商品ID
$productID = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

if ($productID > 0) {
    // 查詢商品資料
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "商品不存在";
        exit();
    }

    // 查詢商品對應的圖片路徑
    $stmt = $conn->prepare("SELECT image_path FROM image_product WHERE ID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $imageResult = $stmt->get_result();
    $image = $imageResult->fetch_assoc();

    $currentImagePath = $image ? $image['image_path'] : 'img/default.jpg'; // 預設圖片路徑

    // 查詢賣家名字
    $sellerID = $product['Seller_id']; // 假設產品表有 seller_id 欄位
    $stmt = $conn->prepare("SELECT Name FROM member WHERE ID = ?");
    $stmt->bind_param("i", $sellerID);
    $stmt->execute();
    $sellerResult = $stmt->get_result();
    $seller = $sellerResult->fetch_assoc();
    $sellerName = $seller ? $seller['Name'] : '未知賣家';

    // 查詢剩餘商品數量
    $remainingQuantity = $product['num']; // 假設產品表有 quantity 欄位

    // 檢查該商品是否已被當前用戶加入最愛
    $favoriteStatus = 0;
    if ($isLoggedIn) {
        $userID = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT COUNT(*) FROM list_liked WHERE customer_id = ? AND seller_id = ?");
        $stmt->bind_param("ii", $userID, $sellerID);
        $stmt->execute();
        $favoriteResult = $stmt->get_result();
        $favoriteCount = $favoriteResult->fetch_row()[0];
        $favoriteStatus = $favoriteCount > 0 ? 1 : 0;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細頁 - <?= htmlspecialchars($product['Product_name']) ?> - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-detail {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .product-detail {
            display: flex;
            justify-content: center; /* 改為讓內容居中對齊 */
            align-items: center; /* 垂直居中 */
            padding: 20px 50px; /* 減少左右邊距 */
            background-color: #f9f9f9;
            gap: 30px; /* 增加圖片與文字間的距離 */
        }

        .product-image img {
            max-width: 350px;
            max-height: 350px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-right: 20px; /* 增加圖片右邊距 */
        }

        .product-info {
            max-width: 500px;
            text-align: left; /* 確保文字左對齊 */
        }

        .product-info h2 {
            font-size: 32px;
            color: #333;
        }

        .product-info p {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px; /* 縮小段落間距 */
        }

        .product-info .price {
            font-size: 24px;
            font-weight: bold;
            color: #ff6f61;
            margin-top: 10px;
        }

        .product-info .add-to-cart button {
            background-color: #ff6f61;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-info .add-to-cart button:hover {
            background-color: #ff5733;
        }

        .favorite-button {
            background-color: #ffcc00;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .favorite-button:hover {
            background-color: #ffbf00;
        }

    </style>
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">
            <a href="index.php">CowPee購物</a>
        </div>
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

    <!-- Product Detail Section -->
    <section class="product-detail">
        <div class="product-image">
            <img src="<?= htmlspecialchars($currentImagePath) ?>" alt="<?= htmlspecialchars($product['Product_name']) ?>">
        </div>
        <div class="product-info">
            <h2><?= htmlspecialchars($product['Product_name']) ?></h2>
            <p><?= htmlspecialchars($product['Description']) ?></p>
            <p>賣家：<?= htmlspecialchars($sellerName) ?></p>
            <p>剩餘數量：<?= htmlspecialchars($remainingQuantity) ?> 件</p>
            <p class="price">NT$<?= htmlspecialchars($product['Price']) ?></p>
            <div class="add-to-cart">
                <a href="add_to_cart.php?id=<?= $product['product_id'] ?>&name=<?= urlencode($product['Product_name']) ?>&price=<?= $product['Price'] ?>">
                    <button>加入購物車</button>
                </a>
            </div>
            <?php if ($isLoggedIn): ?>
                <form action="follow_seller.php" method="POST">
                    <input type="hidden" name="seller_id" value="<?= htmlspecialchars($sellerID) ?>">
                    <?php if ($favoriteStatus): // 已追蹤 ?>
                        <button type="submit" class="favorite-button" style="background-color: #ff6666;">取消追蹤</button>
                    <?php else: // 未追蹤 ?>
                        <button type="submit" class="favorite-button">追蹤賣家</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
