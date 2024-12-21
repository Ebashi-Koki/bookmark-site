
// フォルダ名を編集する関数
// 現在のフォルダ名をテキストボックスに変更し、編集できるようにする
function editFolderName(button) {
    const folderNameElement = document.getElementById('current-folder-name');
    const currentName = folderNameElement.textContent;
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentName;
    
    // 入力ボックスからフォーカスが外れたらフォルダ名を更新する処理
    input.onblur = function() {
        folderNameElement.textContent = input.value;
        input.remove(); // 入力ボックスを削除して編集を終了
    };
    
    // ユーザーがキーを押した時の処理
    input.onkeydown = function(event) {
        // 押されたキーが "Enter" かどうかを確認
        if (event.key === 'Enter') {
            // Enterキーが押された場合、フォルダ名を更新して入力ボックスを削除
            folderNameElement.textContent = input.value;
            input.remove();
        }
    };
    
    folderNameElement.textContent = '';
    folderNameElement.appendChild(input);
    input.focus(); // 自動的に入力状態にする
}

// フォルダに格納されたブックマークを表示する関数
// 選択したフォルダ内のブックマークを画面に表示する
function showBookmarks(link) {
    const folder = link.parentNode; // クリックされたリンクの親要素(フォルダ)を取得
    const folderName = folder.querySelector('a').textContent; // フォルダ名を取得
    const bookmarks = folder.querySelectorAll('.bookmark'); // フォルダ内の全てのブックマークを取得
    const bookmarksContainer = document.getElementById('bookmarks-container');

    // 現在表示しているフォルダ名を更新
    document.getElementById('current-folder-name').textContent = folderName;

    // 古いブックマークを削除
    bookmarksContainer.innerHTML = '';

    // 新しいブックマークを追加
    bookmarks.forEach(bookmark => {
        bookmarksContainer.appendChild(bookmark.cloneNode(true)); // クローンを作成して表示
    });
}

// 新しいフォルダを追加する関数
// サイドバーに新しいフォルダを作成する
function addFolder() {
    const sidebar = document.querySelector('.sidebar'); // サイドバー要素を取得
    const newFolder = document.createElement('div'); // 新しいフォルダ要素を作成
    newFolder.className = 'folder'; // フォルダのクラスを設定

    // フォルダのリンクを作成
    const newFolderLink = document.createElement('a');
    newFolderLink.href = '#';
    newFolderLink.textContent = '新しいフォルダ';
    newFolderLink.onclick = function() {
        showBookmarks(newFolderLink); // クリック時にフォルダの内容を表示
    };

    // サブフォルダ追加ボタンを作成
    const addSubfolderBtn = document.createElement('button');
    addSubfolderBtn.className = 'add-subfolder-btn';
    addSubfolderBtn.textContent = 'サブフォルダ追加';
    addSubfolderBtn.onclick = function() {
        addSubfolder(addSubfolderBtn); // サブフォルダを追加
    };

    // フォルダに要素を追加してサイドバーに挿入
    newFolder.appendChild(newFolderLink);
    newFolder.appendChild(addSubfolderBtn);
    sidebar.appendChild(newFolder);
}

// 新しいサブフォルダを追加する関数
// 選択したフォルダの中にサブフォルダを追加
function addSubfolder(button) {
    const folder = button.parentNode;
    const newSubfolder = document.createElement('div');
    newSubfolder.className = 'subfolder';
    const newSubfolderLink = document.createElement('a');
    newSubfolderLink.href = '#';
    newSubfolderLink.textContent = '新しいサブフォルダ';
    newSubfolderLink.onclick = function() {
        showBookmarks(newSubfolderLink);
    };
    newSubfolder.appendChild(newSubfolderLink);
    folder.appendChild(newSubfolder);
}

// フォルダにブックマークを追加する関数
// 現在表示されているフォルダに新しいブックマークを追加する
function addBookmark(button) {
    const bookmarksContainer = document.getElementById('bookmarks-container');
    const newBookmark = document.createElement('div');
    newBookmark.className = 'bookmark';
    newBookmark.innerHTML = `
        <div class="bookmark-title">新しいブックマーク</div>
        <div class="bookmark-url"><a href="https://www.deeplol.gg/champions" target="_blank">https://www.deeplol.gg/champions</a></div>
        <div class="bookmark-tags">
            <input type="text" placeholder="タグを追加..." onkeydown="addTag(event, this)">
        </div>
    `;
    bookmarksContainer.appendChild(newBookmark);
}

// フォルダを検索する関数
// ユーザーが入力したキーワードに基づいてフォルダをフィルタリング
function searchFolders() {
    const query = document.getElementById('search').value.toLowerCase();
    const folders = document.querySelectorAll('.folder, .subfolder');
    folders.forEach(folder => {
        const text = folder.textContent.toLowerCase();
        // フォルダのテキストが検索キーワードを含むかをチェック
        folder.style.display = text.includes(query) ? '' : 'none';
    });
}

// ドラッグ開始時の処理
// ドラッグ中のフォルダのデータを保存
function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

// ドロップ可能にする処理
// ドロップエリアのデフォルトの動作を無効化
function allowDrop(event) {
    event.preventDefault();
}

// ドロップ時の処理
// ドラッグしたフォルダを新しい場所に移動
function drop(event) {
    event.preventDefault();
    const data = event.dataTransfer.getData("text");
    const folder = document.getElementById(data);
    event.target.appendChild(folder);
}

// タグを追加する関数
// 入力ボックスにタグを追加し、Enterキーで決定
function addTag(event, input) {
    // if文: 押されたキーが Enter かどうかをチェック
    if (event.key === 'Enter') {
        const tag = document.createElement('span');
        tag.textContent = input.value;
        input.parentNode.insertBefore(tag, input);
        input.value = ''; // 入力ボックスを空にする
    }
}

// 通知を表示する関数
// 一時的な通知メッセージを表示
function notify(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// ブックマークをエクスポートする関数
// 現在のブックマークをJSON形式でダウンロード
function exportBookmarks() {
    const bookmarks = [...document.querySelectorAll('.bookmark')].map(bookmark => ({
        title: bookmark.querySelector('.bookmark-title').textContent,
        url: bookmark.querySelector('.bookmark-url a').href,
    }));
    const blob = new Blob([JSON.stringify(bookmarks)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'bookmarks.json';
    link.click();
}

// ブックマークをインポートする関数
// JSONファイルからブックマークを読み込んで表示
function importBookmarks(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        const bookmarks = JSON.parse(e.target.result);
        bookmarks.forEach(bookmark => {
            const bookmarkElement = document.createElement('div');
            bookmarkElement.className = 'bookmark';
            bookmarkElement.innerHTML = `
                <div class="bookmark-title">${bookmark.title}</div>
                <div class="bookmark-url"><a href="${bookmark.url}" target="_blank">${bookmark.url}</a></div>
            `;
            document.querySelector('.content').appendChild(bookmarkElement);
        });
    };
    reader.readAsText(file);
}
