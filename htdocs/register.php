<?php
// XAMPP 資料庫連線參數
$host = 'localhost'; // XAMPP 本地伺服器
$dbname = 'cowpee'; // 替換為您資料庫名稱
$username = 'root'; // XAMPP 預設使用者
$password = ''; // 預設密碼為空

// 連線到資料庫
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("無法連線到資料庫: " . $e->getMessage());
}

// 處理註冊請求
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // 簡單的表單驗證
    if (empty($name) || empty($username) || empty($password) || empty($email)) {
        $error = '所有欄位均為必填';
    } else {
        // 檢查是否有相同的使用者名稱或電子郵件
        $stmt = $pdo->prepare("SELECT * FROM member WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['username'] == $username) {
                $error = '該使用者名稱已被使用';
            } elseif ($user['email'] == $email) {
                $error = '該電子郵件已被註冊';
            }
        } else {
            // 加密密碼暫不使用
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // 插入資料到資料庫
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO member (name, username, password, email, member_status, verified, register_date) 
                    VALUES (?, ?, ?, ?, 'active', 0, NOW())
                ");
                $stmt->execute([$name, $username, $password, $email]);

                header("Location: login.php"); // 註冊成功後導向登入頁面
                exit;
            } catch (PDOException $e) {
                $error = '註冊失敗: ' . $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊頁面 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css"> <!-- 引用統一的外部CSS -->
    <style>
        /* 註冊頁面的表單樣式 */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .login-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            color: black; /* 設置文字顏色為黑色 */
        }

        .submit-group {
            text-align: center;
        }

        .submit-group button, .register-btn {
            background-color: #4CAF50; /* 綠色 */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px; /* 橢圓形 */
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            text-decoration: none; /* 讓超連結看起來像按鈕 */
            display: inline-block;
        }

        .submit-group button:hover, .register-btn:hover {
            background-color: #45a049; /* 當滑鼠滑過時改為較深的綠色 */
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        .footer-links a {
            color: #333;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h2>註冊</h2>
            </div>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="name">姓名</label>
                    <input type="text" id="name" name="name" placeholder="請輸入您的姓名" 
                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                        required>
                </div>
                <div class="input-group">
                    <label for="username">使用者名稱</label>
                    <input type="text" id="username" name="username" placeholder="請輸入您的使用者名稱" 
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                        required>
                </div>
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" placeholder="請輸入密碼" required>
                </div>
                <div class="input-group">
                    <label for="email">電子郵件</label>
                    <input type="email" id="email" name="email" placeholder="請輸入您的電子郵件" 
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                        required>
                </div>
                <div class="submit-group">
                    <button type="submit">註冊</button>
                    <a href="login.php" class="register-btn">返回登入</a>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
