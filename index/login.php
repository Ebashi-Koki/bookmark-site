<!DOCTYPE html>
<html lang="ja">
        <?php
            
            if (isset($_COOKIE["PHPSESSID"])) {
                setcookie("PHPSESSID", '', time() - 1800, '/');
            }

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "bookmark_db";

            // データベース接続
            $conn = new mysqli($servername, $username, $password, $dbname);

            // 接続確認
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

        ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinkNest ログインページ</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        .login-container h1 {
            margin: 0 0 20px;
            font-size: 24px;
            text-align: center;
            color: #333333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            color: #e74c3c;
            font-size: 14px;
        }

        .login-container .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777777;
        }

        .login-container .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container .footer a:hover {
            text-decoration: underline;
        }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>ログイン</h1>
            <form method="POST">

                <?php
                session_start();
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $login_username = $_POST['username'];
                        $login_password = $_POST['password'];
            
                        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                        $stmt->bind_param("s", $login_username); 
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();
            
                        if ($user && password_verify($login_password, $user['password'])) {
                            $_SESSION['user_id'] = $user['id']; 
                            $_SESSION['username'] = $user['username'];
                            $message = "ログイン成功 ようこそ、{$user['username']}さん。";
                            header('Location: index.php');
                            exit();
                        } else {
                            $message = "ユーザー名またはパスワードが間違っています。";
                        }
                    }
                    
                    $conn->close();
                ?>

                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required>
                    <button type="sumit">ログイン</button>
                    <p>アカウントをお持ちでない方は <a href="touroku.php">登録ページ</a> へ</p>
                </div>
            </form>

            
        </div>
    </body>
</html>

