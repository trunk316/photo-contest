<?php
// 共通部分の読み込み
require_once('../app/db.php');
require_once('../app/auth.php');

require_login();

// データの受信
//POSTメソッドで渡されてきたかの確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$_SESSION['error'] = '不正なアクセスです。';
	header('Location: list.php');
	exit;
}

$photo_id = $_POST['photo_id'] ?? null;
$photo_title = $_POST['title'] ?? 'タイトル不明';
$user_name = $_POST['user_name'] ?? '投稿者不明';
$file_path = $_POST['file_path'] ?? '写真が見つかりません';

if (empty($photo_id) || !is_numeric($photo_id)) {
	$_SESSION['error'] = '無効な写真IDです。';
	header('Location: list.php');
	exit;
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

	<h2>投票</h2>
	<div>
		<img src="<?php echo htmlspecialchars($file_path); ?>"
			alt="<?php echo htmlspecialchars($photo_title); ?>">
		<div>
			<p>写真タイトル: <strong><?php echo htmlspecialchars($photo_title); ?></strong></p>
			<p>投稿者: <?php echo htmlspecialchars($user_name); ?></p>
		</div>
		<form method="post" action="../process/vote_process.php">
			<input type="hidden" name="photo_id" value="<?php echo htmlspecialchars($photo_id); ?>">
			<button type="submit" name="vote">この写真に投票する</button>
		</form>
	</div>

	<a href="list.php">戻る</a>
</body>

</html>