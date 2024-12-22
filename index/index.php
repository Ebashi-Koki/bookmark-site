<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkNest</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>LinkNest</h1>
        <div class="header-right">
            <input type="text" id="search" placeholder="検索..." onkeyup="searchFolders()">
            <button onclick="addFolder()">フォルダ追加</button>
            <button>所属グループ</button>
            <button>ユーザ</button>
            <button>ログアウト</button>
        </div>
    </header>
    <div class="main">
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
            ?>
        <div class="sidebar">
            
            <h2>フォルダ</h2>
            <?php
            // フォルダ取得処理
            $folders = [];
            $result = $conn->query("SELECT * FROM folders");
            while ($row = $result->fetch_assoc()) {
                $folders[] = $row;
            }
            $conn->close();
            
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
            $conn->close();
            ?>

            <div class="content-header">
                <h2 id="current-folder-name">ブックマーク</h2>
                <button id="add-bookmark-btn" onclick="addBookmark(this)">ブックマーク追加</button>
                <button id="edit-folder-btn" onclick="editFolderName(this)">名称変更</button>
            </div>
            <div id="bookmarks-container"></div>            
 

            <div class="bookmark" id="bookmark-1" draggabble="true" ondragstart="drag(event)">
               <div class="bookmark-title">新しいブックマーク</div>
               <div class="bookmark-url"><a href="https://www.deeplol.gg/champions" target="_blank">https://www.deeplol.gg/champions</a></div>
               <div class="bookmark-tags">
               <input type="text" placeholder="タグを追加..." onkeydown="addTag(event, this)">
        </div>
            </div>
            

            

            
        </div>

        <div class="right-sidebar">
            <h3>タグ</h3>
            <input type="text" id="search" placeholder="検索..." onkeyup="searchFolders()">
            <div class="" id="">
        </div>

    </div>
    <script src="../js/bookmark.js"></script>
</body>
</html>