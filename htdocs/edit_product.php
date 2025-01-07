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
}

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];  // 新增數量的處理
    $description = $_POST['description'];  // 新增描述的處理
    $currentImagePath = $currentImagePath; // 預設圖片路徑

    // 處理圖片上傳
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        // 檢查檔案是否為圖片
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileSize = $_FILES['product_image']['size'];
        $fileType = $_FILES['product_image']['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'img/'; // 更新為 img 資料夾
            $newFileName = uniqid('product_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $destination = $uploadDir . $newFileName;

            // 移動檔案到上傳資料夾
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // 更新圖片路徑
                $currentImagePath = $destination;

                // 更新 image_path 表中的圖片路徑
                $stmt = $conn->prepare("UPDATE image_product SET image_path = ? WHERE ID = ?");
                $stmt->bind_param("si", $currentImagePath, $productID);
                $stmt->execute();
            } else {
                echo "圖片上傳失敗";
                exit();
            }
        } else {
            echo "請選擇有效的圖片檔案";
            exit();
        }
    }

    // 更新商品資料，包括數量和描述
    $stmt = $conn->prepare("UPDATE product SET Product_name = ?, Price = ?, num = ?, Description = ? WHERE product_id = ?");
    $stmt->bind_param("ssisi", $productName, $price, $quantity, $description, $productID);

    if ($stmt->execute()) {
        header("Location: profile.php"); // 更新成功後跳回個人頁面
        exit();
    } else {
        echo "更新商品失敗";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯商品 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit_product.css">
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

    <main>
        <div class="container">
            <h2>編輯商品</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">商品名稱</label>
                    <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product['Product_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">價格</label>
                    <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['Price']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="quantity">庫存數量</label>
                    <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['num']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">商品描述</label>
                    <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($product['Description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="product_image">商品圖片</label>
                    <input type="file" id="product_image" name="product_image">
                    <p>目前圖片：</p>
                    <img src="<?= $currentImagePath ? htmlspecialchars($currentImagePath) : 'img/default.jpg' ?>" alt="Product Image" width="200px">
                </div>
                <button type="submit" class="submit-btn">儲存更改</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
