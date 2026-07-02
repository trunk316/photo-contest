<?php
// 共通部分の読み込み
require_once('../app/db.php');
require_once('../app/auth.php');

// ログインの確認
require_login();

// ログイン中のユーザーIDを取得
$user_id = $_SESSION['user_id'];

try {
	//現在のユーザー情報(ログインID、表示名、ステータス)を取得
	$pdo = db_connect();
	$stmt = $pdo->prepare('SELECT display_name, login_id, role FROM user WHERE id = :id');
	$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$current_user = $stmt->fetch();

	//ログインidと表示名を格納
	//変更確認画面から戻ってきたときは変更入力されていた文字列を格納(したいけどできない　あとで修正)
	$display_name = $_SESSION['modify_input']['display_name'] ?? $current_user['display_name'];
	$login_id = $_SESSION['modify_input']['login_id'] ?? $current_user['login_id'];

	// エラーメッセージを取得し、クリア
	$error_message = $_SESSION['modify_error'] ?? '';
	unset($_SESSION['modify_error']);
} catch (PDOException $e) {
	error_log($e->getMessage());
	exit('システムエラーが発生しました。');
}
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
	<!--エラーメッセージを表示-->
	<?php if (!empty($error_message)): ?>
		<p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
	<?php endif; ?>
	<p>情報変更入力</p>
	<!--変更を入力-->
	<form method="post" action="modify_confirm.php">
		<p>ログインID:(現在: <?php echo htmlspecialchars($current_user['login_id']); ?>):
			<input type="text" name="login_id" value="<?php echo htmlspecialchars($login_id); ?>">
		</p>
		<p>ユーザー名:(現在: <?php echo htmlspecialchars($current_user['display_name']); ?>):
			<input type="text" name="display_name" value="<?php echo htmlspecialchars($display_name); ?>">
		</p>
		<p>新しいパスワード(変更しない場合は空欄):
			<input type="text" name="password" value="">
		</p>
		<p>ステータス:
			<?php
			if ($current_user['role'] == "viewer") {
				echo "閲覧者";
			} else if ($current_user['role'] == "poster") {
				echo "投稿者";
			}
			?>
			(変更できません)</p>
		<br>
		<input type="submit" value="変更確認へ">
	</form>

	<a href="profile.php">戻る</a>
</body>

</html>