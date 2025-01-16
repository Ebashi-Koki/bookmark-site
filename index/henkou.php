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

// ユーザー情報をデータベースから取得
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($henkou_username, $email);
$stmt->fetch();
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border: 2px solid #cccccc;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        .form-container h1 {
            text-align: center;
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-container button {
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

        .form-container button:hover {
            background-color: #0056b3;
        }

        .form-container .message {
            color: #d9534f;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .form-container .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .form-container .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .form-container .footer a:hover {
            text-decoration: underline;
        }

        .form-container input[type="password"] {
            border: 1px solid #007bff;
        }

        .form-container p {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>ユーザー情報変更</h2>
        <form method="POST" action="">
            <label for="new_username">ユーザー名:</label>
            <input type="text" name="new_username" placeholder="ユーザー名" value="<?php echo htmlspecialchars($henkou_username); ?>" required onfocus="this.select()">
            <label for="new_email">メールアドレス:</label>
            <input type="email" name="new_email" placeholder="メールアドレス" value="<?php echo htmlspecialchars($email); ?>" required onfocus="this.select()">
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
    </div>
</body>
</html>
