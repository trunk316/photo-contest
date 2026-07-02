<?php
// 共通部分の読み込み
require_once('../app/db.php');
require_once('../app/auth.php');

// ログインの確認
require_login();

// ログイン中のユーザーIDとロールを取得
$user_id = $_SESSION['user_id'];
$login_id = $_SESSION['login_id'];
$display_name = $_SESSION['display_name'];
$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>写真コンテスト</title>
</head>

<body>

	<h1>写真コンテスト</h1>

	<h2>プロフィール</h2>
	<p>ユーザーID:<?php echo htmlspecialchars($login_id); ?></p>
	<p>ユーザー名:<?php echo htmlspecialchars($display_name); ?></p>
	<p>パスワード:*********</p>
	<p>ステータス:<?php
			if ($user_role == "viewer") {
				echo "閲覧者";
			} else if ($user_role == "poster") {
				echo "投稿者";
			}
			?></p>

	<p>
		<a href="modify_input.php" class="button">情報変更を入力する</a>
	</p>

	<p>
		<a href="delete_confirm.php" class="button-delete">アカウント削除</a>
	</p>

	<a href="list.php">写真一覧に戻る</a>
	<form method="post" action="../process/auth_process.php">
		<input type="hidden" name="action" value="logout">
		<button type="submit">ログアウト</button>
	</form>


</body>

</html>