<?php
// 共通ファイルの読み込みと認証チェック
require_once('../app/auth.php');

// ログイン必須
require_login();

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
	<p>アカウント削除の確認</p>

	<p style="color: red; font-weight: bold;">
		この操作は取り消すことができません。本当にアカウントを削除しますか?
		<?php if ($user_role == "poster") {
			echo "(投稿したデータは全て消えます。)";
		}

		?>

	</p>

	<hr>

	<form method="post" action="../process/profile_process.php">
		<input type="hidden" name="action" value="delete_execute">
		<input type="submit" name="delete_confirm" value="アカウントを削除する">
	</form>

	<br>

	<p>
		<a href="profile.php">削除せずにプロフィールに戻る</a>
	</p>

</body>

</html>