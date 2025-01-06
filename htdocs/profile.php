<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

// 連接資料庫
$conn = new mysqli("localhost:3300", "root", "", "cowpee");
if ($conn->connect_error) {
    die("資料庫連接失敗：" . $conn->connect_error);
}

// 從資料庫中讀取使用者資料
$stmt = $conn->prepare("SELECT ID, Name, username, password, phone, email, authority, register_date FROM member WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// 如果是賣家，查詢他所新增的商品
$products = [];
if ($userData['authority'] === '賣家權限') {
    // 使用用戶的ID去查找商品
    $userID = $userData['ID'];
    $productStmt = $conn->prepare("SELECT Product_name, Price FROM product WHERE Seller_id = ?");
    $productStmt->bind_param("i", $userID);
    $productStmt->execute();
    $productResult = $productStmt->get_result();

    while ($product = $productResult->fetch_assoc()) {
        $products[] = $product;
    }
}

// 處理新增商品
if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];

    // 插入新商品到資料庫
    $insertStmt = $conn->prepare("INSERT INTO product (Seller_id, Product_name, Price) VALUES (?, ?, ?)");
    $insertStmt->bind_param("isi", $userID, $productName, $price);

    if ($insertStmt->execute()) {
        header("Location: profile.php"); // 商品新增成功後重新載入頁面
        exit();
    } else {
        echo "新增商品失敗，請稍後再試！";
    }
}

$message = ''; // 用來儲存訊息
$messageClass = ''; // 用來儲存訊息的 CSS 類別

if (isset($_POST['update_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    // 驗證舊密碼是否正確
    if ($oldPassword == $userData['password']) {
        // 更新資料庫中的密碼
        $query = "UPDATE member SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $newPassword, $userData['username']);
        if ($stmt->execute()) {
            // 密碼修改成功，顯示對話框並跳轉回主畫面
            unset($_SESSION['username']);
            echo "<script>
                    alert('密碼已成功修改，請重新登入。');
                    window.location.href = 'index.php'; // 重新導向到主畫面
                  </script>";
        } else {
            $message = '密碼修改失敗，請稍後再試！';
            $messageClass = 'error'; // 設定為錯誤訊息類別
        }
    } else {
        $message = '舊密碼錯誤！';
        $messageClass = 'error'; // 設定為錯誤訊息類別
    }
}

// 檢查用戶是否為賣家
$isSeller = $userData['authority'] === '賣家權限';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>個人資訊 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function showContent(section) {
            const sections = document.querySelectorAll('.content');
            sections.forEach(sec => sec.style.display = 'none');
            document.getElementById(section).style.display = 'block';

            const buttons = document.querySelectorAll('.sidebar a');
            buttons.forEach(btn => btn.classList.remove('active'));
            document.getElementById('btn-' + section).classList.add('active');
        }

        function togglePasswordUpdate() {
            const passwordField = document.getElementById('password');
            const oldPasswordField = document.getElementById('old-password');
            const updateButton = document.getElementById('update-password-btn');
            const newPasswordField = document.getElementById('new-password');

            if (passwordField.value !== passwordField.defaultValue) {
                oldPasswordField.style.display = 'block';
                newPasswordField.style.display = 'block';
                updateButton.style.display = 'inline-block';
            } else {
                oldPasswordField.style.display = 'none';
                newPasswordField.style.display = 'none';
                updateButton.style.display = 'none';
            }
        }

        function showPasswordUpdateForm() {
            const passwordUpdateSection = document.getElementById('password-update-section');
            passwordUpdateSection.style.display = 'block';

            const editButton = document.getElementById('edit-password-btn');
            editButton.style.display = 'none';
        }

        function validatePhone() {
            var phoneInput = document.getElementById("phone-input").value;
            var phoneStatus = document.getElementById("phone-status");
            // 檢查手機號碼格式（必須以09開頭且長度為10）
            if (!/^09\d{8}$/.test(phoneInput)) {
                phoneStatus.textContent = "手機號碼格式不正確，請確認手機號碼是以09開頭且為10位數字。";
                phoneStatus.style.color = "red";
            } else {
                // 使用 Ajax 發送請求檢查手機號碼是否已經被註冊
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "validate_phone.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response === "exists") {
                            phoneStatus.textContent = "該手機號碼已經被註冊，請使用其他號碼。";
                            phoneStatus.style.color = "red";
                        } else if (response === "valid") {
                            phoneStatus.textContent = "手機號碼更新成功！";
                            phoneStatus.style.color = "green";
                            // 當更新成功後，重新載入頁面
                            setTimeout(function() {
                                window.location.href = "profile.php"; // 轉跳回個人資訊頁面
                            }, 2000); // 延遲2秒後轉跳，讓用戶看到更新成功的提示
                        } else {
                            phoneStatus.textContent = "手機號碼更新失敗，請稍後再試。";
                            phoneStatus.style.color = "red";
                        }
                    }
                };
                xhr.send("phone=" + phoneInput);
            }
        }

    </script>
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php">CowPee購物</a>
        </div>
        <nav class="menu">
            <a href="cart.php">購物車</a>
            <a href="logout.php">登出</a>
        </nav>
    </header>

    <main style="display: flex;">
        <div class="sidebar" style="width: 200px; padding: 20px; background-color: #f4f4f4; height: 100vh;">
            <h3>個人資訊</h3>
            <a href="javascript:void(0)" id="btn-account" onclick="showContent('account')" class="active">帳號資訊</a>
            <a href="javascript:void(0)" id="btn-coupons" onclick="showContent('coupons')">折價券管理</a>
            <a href="javascript:void(0)" id="btn-sold-products" onclick="showContent('sold-products')" <?php echo $isSeller ? '' : 'style="display:none;"'; ?>>販售商品</a>
            <a href="javascript:void(0)" id="btn-purchase-history" onclick="showContent('purchase-history')">購買紀錄</a>
            <a href="javascript:void(0)" id="btn-order-management" onclick="showContent('order-management')" <?php echo $isSeller ? '' : 'style="display:none;"'; ?>>訂單管理</a>
            <a href="javascript:void(0)" id="btn-store-coupons" onclick="showContent('store-coupons')" <?php echo $isSeller ? '' : 'style="display:none;"'; ?>>商店優惠券管理</a>
        </div>

        <div class="content-section" style="flex-grow: 1; padding: 20px;">
        <section id="account" class="content">
            <h2>帳號資訊</h2>
            <div class="account-info">
                <p><strong>ID：</strong> <?= str_pad(htmlspecialchars($userData['ID']), 8, '0', STR_PAD_LEFT) ?></p>
                <p><strong>姓名：</strong> <?= htmlspecialchars($userData['Name']) ?></p>
                <p><strong>使用者名稱：</strong> <?= htmlspecialchars($userData['username']) ?></p>
                <p>
                    <strong>密碼：</strong>
                    <span id="password-display">********</span>
                    <button type="button" id="edit-password-btn" onclick="showPasswordUpdateForm()">修改</button>
                </p>
                <div id="password-update-section" style="display: none;">
                    <form method="POST">
                        <p>
                            <strong>請輸入舊密碼：</strong>
                            <input type="password" id="old-password" name="old_password" required>
                        </p>
                        <p>
                            <strong>新密碼：</strong>
                            <input type="password" id="new-password" name="new_password" required>
                        </p>
                        <button type="submit" name="update_password">確認修改</button>
                    </form>
                </div>

                <p><strong>電子郵件：</strong> <?= htmlspecialchars($userData['email']) ?></p>
                <p><strong>手機號碼：</strong> 
                    <?php if (empty($userData['phone'])): ?>
                        <input type="text" placeholder="請輸入手機號碼" name="phone" id="phone-input">
                        <button id="validate-phone-btn" onclick="validatePhone()">驗證</button>
                        <span id="phone-status"></span> <!-- 顯示驗證狀態 -->
                    <?php else: ?>
                        <?= htmlspecialchars($userData['phone']) ?>
                    <?php endif; ?>
                </p>
                <p><strong>會員等級：</strong> <?= htmlspecialchars($userData['authority']) ?></p>
                <p><strong>註冊日期：</strong> <?= htmlspecialchars($userData['register_date']) ?></p>
            </div>
        </section>

        <section id="sold-products" class="content" style="display: none;">
            <h2>販售商品</h2>
            <a href="add_product.php" id="add-product-btn" class="add-product-btn">新增商品</a>

            <h3>我的商品：</h3>
            <ul class="product-list">
                <?php
                // 查詢所有商品及其圖片和剩餘數量
                $productStmt = $conn->prepare("SELECT p.Product_name, p.Price, i.image_path, p.num FROM product p LEFT JOIN image_product i ON p.product_id = i.ID WHERE p.Seller_id = ?");
                $productStmt->bind_param("i", $userID);
                $productStmt->execute();
                $productResult = $productStmt->get_result();

                while ($product = $productResult->fetch_assoc()) {
                    $imagePath = $product['image_path'] ? $product['image_path'] : 'img/default.jpg'; // 若沒有圖片，顯示預設圖片
                    $remainingQuantity = $product['num']; // 剩餘數量
                    ?>
                    <li class="product-item">
                        <div class="product-info">
                            <img src="<?php echo $imagePath; ?>" alt="Product Image" class="product-image">
                            <div class="product-details">
                                <p><strong>名稱：</strong> <?php echo htmlspecialchars($product['Product_name']); ?></p>
                                <p><strong>價格：</strong> NT$<?php echo htmlspecialchars($product['Price']); ?></p>
                                <p class="product-quantity"><strong>剩餘數量：</strong> <?php echo $remainingQuantity; ?></p>
                            </div>
                        </div>
                        <button class="modify-button">修改</button>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </section>

        </div>
    </main>

    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
