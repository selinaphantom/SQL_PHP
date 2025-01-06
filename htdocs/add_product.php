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

// 確保用戶是賣家
if ($userData['authority'] !== '賣家權限') {
    header("Location: profile.php");
    exit();
}

// 處理新增商品
if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $num = $_POST['num']; // 商品剩餘數量
    $description = $_POST['description'];
    $category = $_POST['category'];

    // 處理圖片上傳
    $imagePath = ''; // 預設為空字串
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageTmpName = $_FILES['product_image']['tmp_name'];
        $imageName = $_FILES['product_image']['name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

        // 只允許圖片格式
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($imageExt), $allowedExt)) {
            $newImageName = uniqid() . '.' . $imageExt;
            $imagePath = 'img/' . $newImageName;
            move_uploaded_file($imageTmpName, $imagePath); // 儲存圖片
        } else {
            echo "只能上傳圖片格式！";
            exit();
        }
    }

    // 使用 Seller_id 為當前用戶的 ID
    $sellerID = $userData['ID'];
    $publishDate = date('Y-m-d H:i:s'); // 目前的日期時間
    $status = 'Active'; // 可以設定預設為 "Active"
    $isDeleted = 0; // 預設為未刪除

    // 插入新商品到資料庫
    $insertStmt = $conn->prepare("INSERT INTO product (Seller_id, Product_name, Price, num, Description, Publish_data, Mdata, Status, Is_deleted, Category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("isdisssiss", $sellerID, $productName, $price, $num, $description, $publishDate, $publishDate, $status, $isDeleted, $category);

    if ($insertStmt->execute()) {
        // 取得剛插入的 product_id
        $product_id = $insertStmt->insert_id;

        // 插入圖片路徑到 image_product 表格
        if (!empty($imagePath)) {
            $insertImageStmt = $conn->prepare("INSERT INTO image_product (ID, image_path) VALUES (?, ?)");
            $insertImageStmt->bind_param("is", $product_id, $imagePath);

            if ($insertImageStmt->execute()) {
                echo "<script>
                        alert('商品與圖片新增成功！');
                        window.location.href = 'profile.php'; // 成功後回到個人資料頁
                      </script>";
            } else {
                echo "圖片儲存失敗，請稍後再試！";
            }
        } else {
            echo "<script>
                    alert('商品新增成功，但未上傳圖片。');
                    window.location.href = 'profile.php'; // 成功後回到個人資料頁
                  </script>";
        }
    } else {
        echo "新增商品失敗，請稍後再試！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增商品 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            margin: 50px auto;
            width: 80%;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: inline-block;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 20px; /* 圓角 */
            border: 1px solid #ccc;
            font-size: 1.1em;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 1.2em;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        textarea {
            height: 150px;
        }
        .input-container {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
    </style>
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
        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <label for="product_name">商品名稱：</label>
            <input type="text" name="product_name" id="product_name" required>
            <br>
            <label for="price">價格：</label>
            <input type="number" name="price" id="price" required>
            <br>
            <label for="num">庫存數量：</label>
            <input type="number" name="num" id="num" required>
            <br>
            <label for="description">商品描述：</label>
            <textarea name="description" id="description" required></textarea>
            <br>
            <label for="category">商品分類：</label>
            <input type="text" name="category" id="category" required>
            <br>
            <label for="product_image">商品圖片：</label>
            <input type="file" name="product_image" id="product_image" accept="image/*" required>
            <br>
            <button type="submit" name="add_product">新增商品</button>
        </form>

    </main>

    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
