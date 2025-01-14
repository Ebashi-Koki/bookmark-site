<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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

    if ($conn->connect_error) {
        die("データベース接続に失敗しました: " . $conn->connect_error);
    }

    
$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $touroku_username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($touroku_username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $touroku_username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "ユーザー名またはメールアドレスが既に使用されています。";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $touroku_username, $email, $hashed_password);

                if ($stmt->execute()) {
                    $message = "ユーザー登録が完了しました。ログインしてください。";
                    header("Location: login.php"); 
                    exit();
                } else {
                    $message = "登録中にエラーが発生しました。もう一度お試しください。";
                }
                $stmt->close();
            }
        } else {
            $message = "パスワードが一致しません。";
        }
    } else {
        $message = "全てのフィールドを入力してください。";
    }
}

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
<h1>ユーザー登録ページ</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <form action="touroku.php" method="post">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">パスワード（確認用）:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit">登録</button>
    </form>
    <p>既にアカウントをお持ちですか？ <a href="login.php">ログイン</a></p>
</body>
</html>