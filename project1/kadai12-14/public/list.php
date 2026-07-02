<?php

// 共通部分の読み込み
require_once('../app/db.php');
require_once('../app/auth.php');

// ログインの確認
require_login();

// ログイン中のユーザーIDとロールを取得
$user_id = $_SESSION['user_id'];
$display_name = $_SESSION['display_name'];
$user_role = $_SESSION['user_role'];

//完了のメッセージ
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

//エラーメッセージ
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

//写真データの取得
try {
	$pdo = db_connect();
	// photoテーブルとuserテーブルをuser_idで結合し、必要な情報を取得
	$sql = "SELECT p.*, u.display_name AS user_name 
            FROM photo p 
            JOIN user u ON p.user_id = u.id 
            ORDER BY p.created_at DESC";

	$stmt = $pdo->query($sql);
	$photos = $stmt->fetchAll();
} catch (PDOException $e) {
	error_log($e->getMessage());
	$photos = []; // エラー時は空の配列を設定
	$error_message = '写真データの取得中にエラーが発生しました。';
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>写真コンテスト</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<header>
		<h1>写真コンテスト</h1>
		<a href="profile.php">プロフィール</a>
		<form method="post" action="../process/auth_process.php">
			<input type="hidden" name="action" value="logout">
			<button type="submit">ログアウト</button>
		</form>
	</header>


	<p>ようこそ<?php echo htmlspecialchars($display_name); ?>さん</p>

	<?php
	//投稿者に耳表示
	if ($user_role == "poster") {
		echo '<a href="upload.php">写真を投稿する</a>';
	}
	?>

	<!--成功メッセージを表示-->
	<?php if (!empty($success_message)): ?>
		<p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($success_message); ?></p>
	<?php endif; ?>

	<!--エラーメッセージを表示-->
	<?php if (!empty($error_message)): ?>
		<p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
	<?php endif; ?>

	<div>
		<h2>写真一覧</h2>

		<!--まだ写真がないとき-->
		<?php if (empty($photos)): ?>
			<p>まだ写真が投稿されていません。</p>
		<?php else: ?>

			<!--写真の一覧を表示-->
			<div class="photo-list-container">
				<?php foreach ($photos as $photo): ?>
					<div class="photo-item">
						<img src=" <?php echo htmlspecialchars($photo['file_path']); ?>"
							alt="<?php echo htmlspecialchars($photo['title']); ?>">
						<p>写真タイトル :<strong><?php echo htmlspecialchars($photo['title']); ?></strong></p>
						<p>投稿者:<?php echo htmlspecialchars($photo['user_name']); ?></p>
						<p>投票数:<?php echo htmlspecialchars($photo['vote_count']); ?></p>
						<!--閲覧者のみ投票-->
						<div>
							<?php if ($user_role === "viewer"): ?>
								<form method="post" action="vote_confirm.php">
									<button type="submit" name="confirm_vote">投票する</button>
									<input type="hidden" name="photo_id" value="<?php echo htmlspecialchars($photo['id']); ?>">
									<input type="hidden" name="user_name" value="<?php echo htmlspecialchars($photo['user_name']); ?>">
									<input type="hidden" name="title" value="<?php echo htmlspecialchars($photo['title']); ?>">
									<input type="hidden" name="file_path" value="<?php echo htmlspecialchars($photo['file_path']); ?>">
								</form>
							<?php endif; ?>
						</div>
					</div>


				<?php endforeach; ?>

			<?php endif; ?>
			</div>
</body>

</html>