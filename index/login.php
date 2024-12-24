<!DOCTYPE html>
<html lang="ja">
        <?php
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
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];
            
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $message = "ログイン成功 ようこそ、{$user['username']}さん。";
                } else {
                    $message = "ユーザー名またはパスワードが間違っています。";
                }
            }

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $user['username']; 
                header('Location: index.php'); 
                exit;
            } else {
                $message = "ユーザー名またはパスワードが間違っています。";
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
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="sumit">ログイン</button>
                </div>
            </form>
    </body>
</html>

