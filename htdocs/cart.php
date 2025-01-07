<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // 判斷使用者是否登入
if (!$isLoggedIn) {
    header("Location: login.php"); // 如果沒有登入，重定向到登入頁面
    exit();
}

// 取得用戶 ID
$userID = $_SESSION['user_id'];

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 處理數量增減和刪除操作
if (isset($_GET['action'], $_GET['id'])) {
    $itemID = intval($_GET['id']);
    if ($_GET['action'] == 'increase') {
        $stmt = $conn->prepare("UPDATE list_items SET quatity = quatity + 1 WHERE order_id   = ? AND customer_id = ?");
        $stmt->bind_param("ii", $itemID, $userID);
        $stmt->execute();
    } elseif ($_GET['action'] == 'decrease') {
        $stmt = $conn->prepare("UPDATE list_items SET quatity = quatity - 1 WHERE order_id   = ? AND customer_id = ? AND quatity > 1");
        $stmt->bind_param("ii", $itemID, $userID);
        $stmt->execute();
    } elseif ($_GET['action'] == 'remove') {
        $stmt = $conn->prepare("DELETE FROM list_items WHERE order_id  = ? AND customer_id = ?");
        $stmt->bind_param("ii", $itemID, $userID);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// 從 list_items 表讀取購物車資料
$stmt = $conn->prepare("
    SELECT 
        li.order_id, 
        li.quatity, 
        p.Product_name, 
        p.Price, 
        ip.image_path 
    FROM 
        list_items li 
    JOIN 
        product p ON li.product_id = p.product_id 
    JOIN 
        image_product ip ON p.product_id = ip.ID 
    WHERE 
        li.customer_id = ?
");

if (!$stmt) {
    die("SQL 錯誤：" . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購物車 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 購物車頁面風格 */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #000000;
        }

        .logo a {
            color: white;
            font-size: 24px;
            text-decoration: none;
        }

        .menu a {
            margin-left: 20px;
            color: white;
            text-decoration: none;
        }

        .menu a:hover {
            text-decoration: none;
        }

        main {
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .cart-table td {
            vertical-align: middle;
        }

        .cart-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }

        .quantity-controls a {
            padding: 5px 10px;
            background-color: #ff6f61;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .quantity-controls a:hover {
            background-color: #e55b4e;
        }

        .remove-btn {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .remove-btn:hover {
            background-color: #e53935;
        }

        .total-price {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }

        .checkout-btn {
            padding: 10px 20px;
            background-color: #ff6f61;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .checkout-btn:hover {
            background-color: #e55b4e;
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
            <a href="cart.php">購物車</a>
            <span><a href="profile.php">歡迎 <?= $_SESSION['username'] ?></a></span>
            <a href="logout.php">登出</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <h2>購物車</h2>

        <?php if (empty($cartItems)): ?>
            <p>您的購物車是空的。</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>商品</th>
                        <th>數量</th>
                        <th>價格</th>
                        <th>總價</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?php
                                    $imagePath = file_exists('img/' . $item['image_path']) 
                                        ? 'img/' . $item['image_path'] 
                                        : 'img/CowPee.jpeg';
                                ?>
                                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['Product_name']) ?>">
                                <?= htmlspecialchars($item['Product_name']) ?>
                            </td>
                            <td class="quantity-controls">
                                <a href="cart.php?action=increase&id=<?= $item['order_id'] ?>">+</a>
                                <?= $item['quatity'] ?>
                                <a href="cart.php?action=decrease&id=<?= $item['order_id'] ?>">-</a>
                            </td>
                            <td>NT$<?= number_format($item['Price'], 0) ?></td>
                            <td>NT$<?= number_format($item['Price'] * $item['quatity'], 0) ?></td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= $item['order_id'] ?>" class="remove-btn">刪除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="total-price">總計：NT$<?= number_format(array_sum(array_map(function ($item) {
                return $item['Price'] * $item['quatity'];
            }, $cartItems)), 0) ?></p>

            <!-- 結帳連結 -->
            <a href="payment.php" class="checkout-btn">結帳</a>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
