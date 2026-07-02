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
} catch (PDOException $e) {
	// データベースエラーが発生した場合
	error_log($e->getMessage());
	$_SESSION['error'] = 'システムエラーが発生しました。';
	header('Location: ../public/login.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// データ受信
	$new_display_name = $_POST['display_name'] ?? '';
	$new_login_id = $_POST['login_id'] ?? '';
	$new_password = $_POST['password'] ?? '';

	// 空欄かチェックする
	if (empty($new_display_name) || empty($new_login_id)) {
		$error_message = 'ユーザー名とログインIDは必須です。';
	}

	// ログインIDの重複チェック (現在のユーザーIDを除外)
	if (empty($error_message) && $new_login_id !== $current_user['login_id']) {
		$stmt = $pdo->prepare('SELECT id FROM user WHERE login_id = :login_id AND id != :user_id');
		$stmt->bindValue(':login_id', $new_login_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetch()) {
			$error_message = 'そのログインIDは既に他のユーザーに使用されています。';
		}
	}

	// エラーがある場合、セッションにエラーと入力値を保存して入力画面に戻る
	if (!empty($error_message)) {
		$_SESSION['modify_error'] = $error_message;
		$_SESSION['modify_input'] = [
			'display_name' => $new_display_name,
			'login_id' => $new_login_id,
		];
		header('Location: modify_input.php');
		exit;
	}

	// パスワードが入力されたかチェック
	$is_password_modified = !empty($new_password);

	// チェックを通過したデータをセッションに一時保存
	$_SESSION['modify_data'] = [
		'login_id' => $new_login_id,
		'display_name' => $new_display_name,
		'password' => $new_password,
	];
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
	<!--変更を入力-->
	<p>情報変更確認</p>
	<p>ログインID:<?php echo htmlspecialchars($current_user['login_id']); ?> =>
		<?php echo htmlspecialchars($_SESSION['modify_data']['login_id']); ?>
	</p>
	<p>ユーザー名:<?php echo htmlspecialchars($current_user['display_name']); ?>: =>
		<?php echo htmlspecialchars($_SESSION['modify_data']['display_name']); ?>
	</p>
	<p>パスワード:*********
		<?php echo $is_password_modified ? '【変更あり】' : '変更なし'; ?>
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
	<form method="post" action="../process/profile_process.php">
		<input type="hidden" name="action" value="modify_execute">
		<input type="submit" name="modify_confirm" value="変更を確定する">
	</form>

	<a href="modify_input.php">修正</a>
	<a href="profile.php">変更せずプロフィールに戻る</a>
</body>

</html>