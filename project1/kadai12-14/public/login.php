<?php

//共通部分の読み込み
require_once('../app/auth.php');

if (is_logged_in()) {
	header('Location: list.php');
	exit;
}

//エラーメッセージを取得する
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
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
	<!--ログイン情報を入力-->
	<form method="post" action="../process/auth_process.php">
		<p>ログインID<input type="text" name="login_id"></p>
		<p>パスワード<input type="password" name="password"></p>
		<input type="submit" value="ログイン">
	</form>
	<a href="register.php">新規アカウント登録へ</a>

	<a href="index.php">戻る</a>
</body>

</html>