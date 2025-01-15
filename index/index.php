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
            <button>所属グループ</button>

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

            <button>ログアウト</button>
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
            $bookmarks = [];
            $result = $conn->query("SELECT * FROM bookmarks");
            while ($row = $result->fetch_assoc()) {
                $bookmarks[] = $row;
            }
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