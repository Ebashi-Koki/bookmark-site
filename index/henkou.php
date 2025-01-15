<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmark_db";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続確認
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = "";

    if (!empty($_POST['new_username'])) {
        $new_username = trim($_POST['new_username']);
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $message .= "ユーザー名が変更されました。<br>";
        } else {
            $message .= "ユーザー名の変更に失敗しました。<br>";
        }
        $stmt->close();
    }

    if (!empty($_POST['new_email'])) {
        $new_email = trim($_POST['new_email']);
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $message .= "メールアドレスが変更されました。<br>";
        } else {
            $message .= "メールアドレスの変更に失敗しました。<br>";
        }
        $stmt->close();
    }

    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $message .= "パスワードが変更されました。<br>";
            } else {
                $message .= "パスワードの変更に失敗しました。<br>";
            }
            $stmt->close();
        } else {
            $message .= "新しいパスワードと確認パスワードが一致しません。<br>";
        }
    }

    echo $message;

    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー情報変更</title>
</head>
<body>

<h2>ユーザー情報変更</h2>
<p>現在のユーザー名: <?php echo htmlspecialchars($user['username']); ?></p>
<p>現在のメールアドレス: <?php echo htmlspecialchars($user['email']); ?></p>

<form method="POST" action="">
    <div>
        <label for="new_username">新しいユーザー名:</label>
        <input type="text" id="new_username" name="new_username">
    </div>
    <div>
        <label for="new_email">新しいメールアドレス:</label>
        <input type="email" id="new_email" name="new_email">
    </div>
    <div>
        <label for="new_password">新しいパスワード:</label>
        <input type="password" id="new_password" name="new_password">
    </div>
    <div>
        <label for="confirm_password">確認用パスワード:</label>
        <input type="password" id="confirm_password" name="confirm_password">
    </div>
    <div>
        <button type="submit">変更</button>
    </div>
</form>

</body>
</html>
