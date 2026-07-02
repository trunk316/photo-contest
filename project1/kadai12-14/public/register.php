<?php

// 共通部分の読み込み
require_once('../app/auth.php');

//エラーメッセージを取得する
$error_message = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>写真コンテスト</title>
</head>

<body>

	<h1>写真コンテスト</h1>
	<?php if (!empty($error_message)): ?>
		<!--エラーメッセージを表示-->
		<p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
	<?php endif; ?>

	<!--登録情報を入力-->
	<form method="post" action="../process/register_process.php">
		<p>ログインID:<input type="text" name="login_id"></p>
		<p>ユーザー名:<input type="text" name="display_name"></p>
		<p>パスワード:<input type="password" name="password"></p>
		<p>ステータス:</p>
		<input type="radio" name="role" value="poster">
		<label>投稿者</label>
		<input type="radio" name="role" value="viewer" checked>
		<label>閲覧者</label>
		<br>
		<input type="submit" value="登録">
	</form>
	<a href="login.php">ログインへ</a>

	<a href="index.php">戻る</a>
</body>

</html>