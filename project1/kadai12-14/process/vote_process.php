<?php

//写真に投票する処理

//1.データベース接続,認証
require_once('../app/db.php');
require_once('../app/auth.php');

require_login();

if (get_role() !== 'viewer') {
	$_SESSION['error'] = '投票権限がありません。';
	header('Location: ../public/list.php');
	exit;
}

//2.データ受信
//POSTメソッドで渡されてきたかの確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$_SESSION['error'] = '不正なアクセスです。';
	header('Location: ../public/list.php'); // 不正アクセスは一覧に戻す
	exit;
}

$photo_id = $_POST['photo_id'] ?? null;

//3.DBにを更新
try {
	$pdo = db_connect();

	// 3.1 投票数に+1する
	$stmt = $pdo->prepare('UPDATE photo SET vote_count = vote_count + 1 WHERE id = :photo_id');
	$stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
	$stmt->execute();

	// 3.2 更新完了と同時に写真一覧へ遷移
	$_SESSION['success_message'] = '投票が完了しました！';
	header('Location: ../public/list.php');
	exit;
} catch (PDOException $e) {
	// データベースエラーが発生した場合
	error_log($e->getMessage());
	$_SESSION['error'] = '投票処理中にシステムエラーが発生しました。';
	header('Location: ../public/list.php');
	exit;
}
