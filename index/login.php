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
                ?>

                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required>
                    <button type="sumit">ログイン</button>
                </div>
            </form>

            
        </div>
    </body>
</html>

