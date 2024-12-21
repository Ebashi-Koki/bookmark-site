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
        <div class="left-sidebar">
            <h2>フォルダ</h2>
            <!-- データベース接続部分をコメントアウト -->
            <?php
            echo "しね";
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

            // フォルダ取得処理
            $folders = [];
            $result = $conn->query("SELECT * FROM folders");
            while ($row = $result->fetch_assoc()) {
                $folders[] = $row;
            }
            $conn->close();

            // 取得した想定でデータを直接埋め込む
            ?>
            <div class="folder" id="folder-1" draggable="true" ondragstart="drag(event)">
                <a href="#" onclick="showBookmarks(this)">フォルダ 1</a>
                <button class="add-subfolder-btn" onclick="addSubfolder(this)">サブフォルダ追加</button>
            </div>
            <div class="folder" id="folder-2" draggable="true" ondragstart="drag(event)">
                <a href="#" onclick="showBookmarks(this)">フォルダ 2</a>
                <button class="add-subfolder-btn" onclick="addSubfolder(this)">サブフォルダ追加</button>
                <div class="subfolder" id="folder-3" draggable="true" ondragstart="drag(event)">
                    <a href="#" onclick="showBookmarks(this)">サブフォルダ 2-1</a>
                </div>
                <div class="subfolder" id="folder-4" draggable="true" ondragstart="drag(event)">
                    <a href="#" onclick="showBookmarks(this)">サブフォルダ 2-2</a>
                </div>
            </div>
            <div class="folder" id="folder-5" draggable="true" ondragstart="drag(event)">
                <a href="#" onclick="showBookmarks(this)">フォルダ 3</a>
                <button class="add-subfolder-btn" onclick="addSubfolder(this)">サブフォルダ追加</button>
            </div>
        </div>
        <div class="content">
            <div class="content-header">
                <h2 id="current-folder-name">ブックマーク</h2>
                <button id="add-bookmark-btn" onclick="addBookmark(this)">ブックマーク追加</button>
                <button id="edit-folder-btn" onclick="editFolderName(this)">名称変更</button>
            </div>
            <div id="bookmarks-container"></div>
        </div>
        <div class="right-sidebar">
            <h3>タグ</h3>

        </div>
    </div>
    <script src="../js/bookmark.js"></script>
</body>
</html>