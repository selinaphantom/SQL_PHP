<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - CowPee購物</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 登入按鈕樣式 */
        .submit-group button {
            background-color: #4CAF50; /* 綠色 */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px; /* 橢圓形 */
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .submit-group button:hover {
            background-color: #45a049; /* 當滑鼠滑過時改為較深的綠色 */
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
            <a href="index.php">首頁</a>
        </nav>
    </header>

    <!-- Login Form -->
    <main>
        <section class="login-form">
            <h2>登入</h2>
            <?php
            // 檢查是否有錯誤訊息
            if (isset($_GET['error'])) {
                echo '<p style="color: red;">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            ?>
            <form action="process-login.php" method="POST">
                <div class="input-group">
                    <label for="username">帳號：</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="input-group">
                    <label for="password">密碼：</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="submit-group">
                    <button type="submit">登入</button>
                    <button type="button" class="register-btn" onclick="handleRegister()">註冊</button>
                </div>
                
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 CowPee購物 - 所有權利保留。</p>
    </footer>
</body>
</html>
<script>
    function handleRegister() {
        //alert('導向註冊流程或顯示註冊表單');
        // 或者可以跳轉到註冊頁面
        window.location.href = "register.php";
    }
</script>