<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // 判斷使用者是否登入
if (!$isLoggedIn) {
    header("Location: login.php"); // 如果沒有登入，重定向到登入頁面
    exit();
}

// 假設購物車資料存在於 session 中
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// 計算總價
$totalPrice = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $cartItems));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>付款 - CowPee購物</title>
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
                            <?php foreach ($cartItems as $id => $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>NT$<?= number_format($item['price'], 0) ?></td>
                                    <td>NT$<?= number_format($item['price'] * $item['quantity'], 0) ?></td>
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
                    <form action="process_payment.php" method="POST">
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
                    <input type="text" id="card-number" placeholder="1234 5678 9012 3456" class="input-field"><br>

                    <label for="expiry-month">年/月</label>
                    <input type="text" id="expiry-month" placeholder="MM/YY" class="input-field"><br>

                    <label for="security-code">安全碼</label>
                    <input type="text" id="security-code" placeholder="CVC" class="input-field"><br>

                    <!-- 記住我的勾選框 -->
                    <div class="remember-me">
                        <label for="remember-card-info">記住我的信用卡資訊</label>
                        <input type="checkbox" id="remember-card-info">
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
