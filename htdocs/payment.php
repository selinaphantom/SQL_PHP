<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // 判斷使用者是否登入
if (!$isLoggedIn) {
    header("Location: login.php"); // 如果沒有登入，重定向到登入頁面
    exit();
}

$userID = $_SESSION['user_id']; // 假設使用者的 user_id 儲存在 session 中

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 查詢該使用者的購物車資料
$sql = "SELECT li.product_id, li.quatity, p.Product_name, p.Price, p.Seller_id  
        FROM list_items li
        JOIN product p ON li.product_id = p.product_id
        WHERE li.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// 將資料存入陣列
$cartItems = [];
$sellerIDs = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $sellerIDs[] = $row['Seller_id']; // 儲存賣家 ID
}

// 計算總價
$totalPrice = array_sum(array_map(function ($item) {
    return $item['Price'] * $item['quatity'];
}, $cartItems));

// 檢查是否有儲存信用卡資訊
$sql_card = "SELECT credit_card, credit_card_expiry, credit_card FROM credit_card WHERE customer_id = ?";
$stmt_card = $conn->prepare($sql_card);
$stmt_card->bind_param("i", $userID);
$stmt_card->execute();
$cardResult = $stmt_card->get_result();
$cardInfo = $cardResult->fetch_assoc();

// 處理信用卡資訊的儲存
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];
    $couponCode = $_POST['coupon'] ?? null; // 優惠券代碼

    // 優惠券檢查
    $couponID = null;
    if ($couponCode) {
        $couponSql = "SELECT ID FROM coupon WHERE code = ?";
        $couponStmt = $conn->prepare($couponSql);
        $couponStmt->bind_param("s", $couponCode);
        $couponStmt->execute();
        $couponResult = $couponStmt->get_result();
        if ($couponRow = $couponResult->fetch_assoc()) {
            $couponID = $couponRow['ID'];
        }
    }

    // 設定運送方式（如果付款方式不是貨到付款）
    $shippingMethod = ($paymentMethod !== "cash_on_delivery") ? "CowPee速送6小時" : $_POST['shipping_method'];

    // 計算費用（假設費用等於總價）
    $fee = $totalPrice;

    // 得到賣家ID
    $sellerID = reset($sellerIDs); // 假設所有商品來自同一賣家，如果不是，需進行其他處理

    // 建立訂單
    $insertOrderSql = "INSERT INTO order_ (customer_id, payment_method, ship_date, fee, taking_method, address, order_date, seller_id) 
                       VALUES (?, ?, NOW(), ?, ?, ?, NOW(), ?)";
    $insertOrderStmt = $conn->prepare($insertOrderSql);
    $insertOrderStmt->bind_param("isissi", $userID, $paymentMethod, $fee, $shippingMethod, $address, $sellerID);
    $insertOrderStmt->execute();

    // 獲取新訂單的 order_id
    $orderID = $conn->insert_id;

    // 更新 list_items 表格中的 order_id
    $updateOrderIDSql = "UPDATE list_items SET order_id = ? WHERE customer_id = ?";
    $updateOrderIDStmt = $conn->prepare($updateOrderIDSql);
    $updateOrderIDStmt->bind_param("ii", $orderID, $userID);
    $updateOrderIDStmt->execute();

    // 扣除每個商品的數量
    foreach ($cartItems as $item) {
        $productID = $item['product_id'];
        $quantityBought = $item['quantity'];

        // 查詢產品的現有庫存數量
        $productSql = "SELECT num FROM product WHERE product_id = ?";
        $productStmt = $conn->prepare($productSql);
        $productStmt->bind_param("i", $productID);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $product = $productResult->fetch_assoc();

        if ($product) {
            $newQuantity = $product['num'] - $quantityBought;

            // 更新產品的剩餘庫存數量
            $updateProductSql = "UPDATE product SET num = ? WHERE product_id = ?";
            $updateProductStmt = $conn->prepare($updateProductSql);
            $updateProductStmt->bind_param("ii", $newQuantity, $productID);
            $updateProductStmt->execute();
        }
    }

    // 如果選擇記住信用卡資訊，新增信用卡資料
    if ($_POST['remember-card-info'] == 1) {
        $cardNumber = $_POST['card-number'];
        $expiryMonth = $_POST['expiry-month'];
        $securityCode = $_POST['security-code'];

        // 插入新的信用卡資料
        $insertCardSql = "INSERT INTO credit_card (customer_id, credit_card, credit_code) 
                          VALUES (?, ?, ?)";
        $insertCardStmt = $conn->prepare($insertCardSql);
        $insertCardStmt->bind_param("iii", $userID, $cardNumber, $securityCode);
        $insertCardStmt->execute();
    }

    // 清除購物車
    $clearCartSql = "DELETE FROM list_items WHERE customer_id = ?";
    $clearCartStmt = $conn->prepare($clearCartSql);
    $clearCartStmt->bind_param("i", $userID);
    $clearCartStmt->execute();

    // 顯示付款成功訊息並重定向到首頁
    echo "<script>alert('付款成功！感謝您的訂購。'); window.location.href = 'index.php';</script>";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>付款 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_payment.css">
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
        <h2>付款</h2>
        <!-- 付款頁面 -->
        <div class="container">
            <!-- 左側: 訂單總覽 -->
            <div class="left-side">
                <div class="order-summary">
                    <h3>訂單總覽</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>商品名稱</th>
                                <th>數量</th>
                                <th>價格</th>
                                <th>總價</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['Product_name']) ?></td>
                                    <td><?= $item['quatity'] ?></td>
                                    <td>NT$<?= number_format($item['Price'], 0) ?></td>
                                    <td>NT$<?= number_format($item['Price'] * $item['quatity'], 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="total-price">總計：NT$<?= number_format($totalPrice, 0) ?></p>
                </div>
            </div>

            <!-- 右側: 收件人資訊與付款方式 -->
            <div class="right-side">
                <!-- 收件人資訊 -->
                <div class="shipping-info">
                    <h3>收件人資訊</h3>
                    <form action="" method="POST">
                        <label for="name">姓名：</label>
                        <input type="text" id="name" name="name" required class="input-field"><br><br>
                        <label for="address">地址：</label>
                        <input type="text" id="address" name="address" required class="input-field"><br><br>
                        <label for="phone">電話：</label>
                        <input type="text" id="phone" name="phone" required class="input-field"><br><br>
                </div>

                <!-- 優惠券輸入 -->
                <div class="coupon-code">
                    <h3>優惠券</h3>
                    <label for="coupon">輸入優惠券代碼：</label>
                    <input type="text" id="coupon" name="coupon" class="input-field">
                </div>

                <!-- 付款方式 -->
                <div class="payment-method">
                    <h3>付款方式</h3>
                    <label for="payment_method">選擇付款方式：</label>
                    <select id="payment_method" name="payment_method" onchange="togglePaymentFields()">
                        <option value="" disabled selected>請選擇付款方式</option>
                        <option value="cash_on_delivery">貨到付款</option>
                        <option value="credit_card">信用卡</option>
                        <option value="paypal">PayPal</option>
                        <option value="atm">ATM 轉帳</option>
                    </select>
                </div>

                <!-- 顯示信用卡輸入框 -->
                <div id="credit-card-fields" class="credit-card-fields">
                    <h3>信用卡資訊</h3>
                    <label for="card-number">卡號</label>
                    <input type="text" id="card-number" name="card-number" value="<?= htmlspecialchars($cardInfo['credit_card_number'] ?? '') ?>" placeholder="1234 5678 9012 3456" class="input-field"><br>

                    <label for="expiry-month">年/月</label>
                    <input type="text" id="expiry-month" name="expiry-month" value="<?= htmlspecialchars($cardInfo['credit_card_expiry'] ?? '') ?>" placeholder="MM/YY" class="input-field"><br>

                    <label for="security-code">安全碼</label>
                    <input type="text" id="security-code" name="security-code" value="<?= htmlspecialchars($cardInfo['credit_card_security_code'] ?? '') ?>" placeholder="CVC" class="input-field"><br>

                    <!-- 記住我的勾選框 -->
                    <div class="remember-me">
                        <label for="remember-card-info">記住我的信用卡資訊</label>
                        <input type="checkbox" id="remember-card-info" name="remember-card-info" <?= isset($cardInfo['credit_card_number']) ? 'checked' : '' ?>>
                    </div>
                </div>

                <!-- 顯示配送選項 -->
                <div id="shipping-options" class="shipping-options">
                    <label for="shipping_method">選擇配送方式：</label>
                    <select id="shipping_method" name="shipping_method">
                        <option value="black_cat">黑貓宅急便</option>
                        <option value="family_mart">全家包裹運送</option>
                        <option value="seven_eleven">7-11包裹運送</option>
                        <option value="cowpee_delivery">CowPee速送6小時</option>
                    </select>
                </div><br>

                <!-- 確認結帳 -->
                <button type="submit" class="checkout-btn">確認結帳</button>
                </form>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>

    <script>
        function togglePaymentFields() {
            var paymentMethod = document.getElementById("payment_method").value;
            var creditCardFields = document.getElementById("credit-card-fields");
            var shippingOptions = document.getElementById("shipping-options");
            creditCardFields.style.display = "none";
            shippingOptions.style.display = "none";
            // 只有當選擇「信用卡」時顯示信用卡資訊欄位
            if (paymentMethod === "credit_card") {
                creditCardFields.style.display = "block";
            }
            if(paymentMethod === "cash_on_delivery"){
                shippingOptions.style.display = "block";
            }
        }

        // 讓「請選擇付款方式」是預設選項
        document.getElementById("payment_method").value = "";
    </script>
</body>
</html>
