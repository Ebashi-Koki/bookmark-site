<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkNest</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
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

            session_start();
            if (!isset($_SESSION['username'])) {
            header('Location: login.php');
            exit;
            }
        ?>

    <header>
        <h1>LinkNest</h1>
        <div class="header-right">
            <input type="text" id="search" placeholder="検索..." onkeyup="searchFolders()">
            <button onclick="addFolder()">フォルダ追加</button>

            <?php
            $user_id = $_SESSION['user_id'];
            $groups = [];
            $stmt = $conn->prepare("SELECT groups.id, groups.name  FROM groups  JOIN user_groups ON groups.id = user_groups.group_id  WHERE user_groups.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($group_id, $group_name);
            while ($stmt->fetch()) {
                $groups[] = ['id' => $group_id, 'name' => $group_name];
            }
            $stmt->close();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
                $group_name = $_POST['group_name'];
                $stmt = $conn->prepare("INSERT INTO groups (name) VALUES (?)");
                $stmt->bind_param("s", $group_name);
                $stmt->execute();
                $new_group_id = $stmt->insert_id;
                $stmt->close();
                
                $stmt = $conn->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $user_id, $new_group_id);
                $stmt->execute();
                $stmt->close();
            }

            // if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_group'])) {
            //     $group_id = $_POST['group_id'];
            //     $stmt = $conn->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)");
            //     $stmt->bind_param("ii", $user_id, $group_id);
            //     $stmt->execute();
            //     $stmt->close();
            // }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_invite'])) {
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $groupId = intval($_POST['group_id']);
            
                if ($email && $groupId) {
                    $inviteLink = "http://yourdomain.com/group_invite.php?group_id=$groupId&email=" . urlencode($email);
            
                    $subject = "グループ招待";
                    $message = "以下のリンクからグループに参加できます:\n\n$inviteLink";
                    $headers = "From: no-reply@yourdomain.com";
            
                    if (mail($email, $subject, $message, $headers)) {
                        $successMessage = "招待メールを送信しました。";
                    } else {
                        $errorMessage = "招待メールの送信に失敗しました。";
                    }
                } else {
                    $errorMessage = "有効なメールアドレスを入力してください。";
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['group_id'], $_GET['email'])) {
                $groupId = intval($_GET['group_id']);
                $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
            
                if ($email && $groupId) {
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->bind_result($userId);
                    if ($stmt->fetch()) {
                        $stmt->close();
                        $stmt = $conn->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $userId, $groupId);
                        if ($stmt->execute()) {
                            $successMessage = "グループに参加しました。";
                        } else {
                            $errorMessage = "グループ参加に失敗しました。";
                        }
                        $stmt->close();
                    } else {
                        $errorMessage = "招待されたメールアドレスが見つかりません。";
                    }
                } else {
                    $errorMessage = "無効なリンクです。";
                }
            }

            ?>

            <label for="group-modal-toggle" class="group-button">グループ</label>
            <input type="checkbox" id="group-modal-toggle" class="modal-toggle">
            <div class="modal">
                <div class="modal-content">
                    <label for="group-modal-toggle" class="close-modal-button">&times;</label>
                    <h2>グループ作成・加入</h2>
                    <form method="POST" action="">
                        <input type="text" name="group_name" placeholder="新しいグループ名" required>
                        <button type="submit" name="create_group">グループ作成</button>
                    </form>
                    <form method="POST" action="">
                        <!-- <h3>既存のグループに加入</h3>
                        <select name="group_id">
                            <?php
                            // $stmt = $conn->prepare("SELECT id, name FROM groups");
                            // $stmt->execute();
                            // $stmt->bind_result($group_id, $group_name);
                            // while ($stmt->fetch()) {
                            //     echo "<option value=\"$group_id\">$group_name</option>";
                            // }
                            // $stmt->close()
                            ?> 
                        </select>
                        <button type="submit" name="join_group">加入</button> -->

                        <?php if (isset($successMessage)): ?>
                            <div class="message success"><?php echo $successMessage; ?></div>
                        <?php endif; ?>

                        <?php if (isset($errorMessage)): ?>
                            <div class="message error"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>

                        <h2>グループ招待メールを送信</h2>
                            <input type="email" name="email" placeholder="招待するメールアドレス" required>
                            <input type="number" name="group_id" placeholder="グループID" required>
                            <button type="submit" name="send_invite">招待メールを送信</button>
                    </form>
                </div>
            </div>


            <?php
        
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
            }
            
            $user_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            ?>

            <label for="user-modal-toggle" class="user-button">ユーザ</label>
            <input type="checkbox" id="user-modal-toggle" class="modal-toggle">
            <div class="modal">
                <div class="modal-content">
                    <label for="user-modal-toggle" class="close-modal-button">&times;</label>
                    <form method="POST">
                        <p>ユーザー名: <?php echo htmlspecialchars($user['username']); ?></p>
                        <p>メールアドレス: <?php echo htmlspecialchars($user['email']); ?></p>
                    </form>
                    <form action="henkou.php" method="GET">
                        <input type="submit" value="ユーザー情報を変更">
                    </form>
                </div>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, 
                        $params["path"], $params["domain"], 
                        $params["secure"], $params["httponly"]
                    );
                }
                header("Location: login.php");
                exit;
            }
            ?>

            <div class="logout-container">
                <form method="POST" action="">
                    <button type="submit" name="logout">ログアウト</button>
                </form>
            </div> 
            
        </div>
    </header>
    <div class="main">
        <div class="sidebar">
            <h2>フォルダ</h2>
            <?php
            // フォルダ取得処理
            $folders = [];
            $result = $conn->query("SELECT * FROM folders");
            while ($row = $result->fetch_assoc()) {
                $folders[] = $row;
            }
            
            // 取得した想定でデータを直接埋め込む
            ?>
            
            <?php foreach ($folders as $folder): ?>
            <div class="folder" id="folder-1" draggable="true" ondragstart="drag(event)">
            <a href="#" onclick="showBookmarks(this)"><?php echo htmlspecialchars($folder['name']); ?></a>
                <button class="add-subfolder-btn" onclick="addSubfolder(this)">サブフォルダ追加</button>
            </div>
            <?php endforeach; ?>

            
        </div>

        <div class="content">
            <?php
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT id, url, title FROM bookmarks WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $bookmarks = [];
            while ($row = $result->fetch_assoc()) {
                $bookmarks[] = $row;
            }
            $stmt->close();
            ?>

            <div class="content-header">
                <h2 id="current-folder-name">ブックマーク</h2>
                <button id="add-bookmark-btn" onclick="addBookmark(this)">ブックマーク追加</button>
                <button id="edit-folder-btn" onclick="editFolderName(this)">名称変更</button>
            </div>
            <div id="bookmarks-container"></div>            
 
            <?php foreach ($bookmarks as $bookmark): ?>
                <div class="bookmark" id="bookmark-1" draggabble="true" ondragstart="drag(event)">
                   <div class="bookmark-title">
                        <?php echo htmlspecialchars($bookmark['title']); ?>
                   </div>
                   <div class="bookmark-url">
                        <a href="<?php echo htmlspecialchars($bookmark['url']); ?>" target="_blank">
                            <?php echo htmlspecialchars($bookmark['url']); ?>
                        </a>
                   </div>
                   <div class="bookmark-tags">
                        <input type="text" placeholder="タグを追加..." onkeydown="addTag(event, this)">
                   </div>
               </div>
            <?php endforeach; ?>
        </div>

        <div class="right-sidebar">
            <h3>タグ</h3>
            <input type="text" id="search" placeholder="検索..." onkeyup="searchFolders()">
            <div class="" id="">
            <?php
            $sql = "SELECT * FROM bookmarks ORDER BY tags ASC";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                $tags = [];
                while($row = $result->fetch_assoc()) {
                    $tags[] = $row;
                }
            } else {
                $tags = [];
            }
            ?>
            <h4>タグ一覧</h4>
            <?php if (!empty($tags)): ?>
                <table>
                    <tbody>
                        <?php foreach ($tags as $tag): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tag['tags'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>タグが見つかりません。</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="../js/bookmark.js"></script>
</body>
</html>