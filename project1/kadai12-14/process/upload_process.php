<?php

//写真を投稿する処理

//1.データベース接続,認証
require_once('../app/db.php');
require_once('../app/auth.php');

require_login();

if (get_role() !== 'poster') {
	$_SESSION['upload_error'] = '写真投稿権限がありません。';
	header('Location: ../public/list.php'); // 権限がない場合は一覧に戻す
	exit;
}

//もろもろの設定
$user_id = $_SESSION['user_id'];
$upload_dir = '../uploads/'; //写真のアップロード先
$error_message = '';

//2.データ受信
//POSTメソッドで渡されてきたかの確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../public/upload.php');
	exit;
}
$title = $_POST['title'] ?? '';
$file_info = $_FILES['photo'] ?? null;

// 3. 空欄かチェック
if (empty($title)) {
	$error_message = '写真タイトルは必須です。';
} elseif (empty($file_info) || $file_info['error'] === UPLOAD_ERR_NO_FILE) {
	$error_message = '写真ファイルを選択してください。';
} elseif ($file_info['error'] !== UPLOAD_ERR_OK) {
	$error_message = 'ファイルのアップロードに失敗しました。';
}

if (!empty($error_message)) {
	// エラー時はセッションに保存してフォームに戻す
	$_SESSION['upload_error'] = $error_message;
	// タイトルを再入力の手間を省くためセッションに一時保存
	$_SESSION['upload_input'] = ['title' => $title];
	header('Location:../public/upload.php');
	exit;
}

// 4. DBにファイルを挿入
try {
	$pdo = db_connect();

	// 4.1 ユニークなファイル名を生成 (タイムスタンプ + ランダム値 + 拡張子)
	$ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
	$new_filename = time() . uniqid() . '.' . $ext;
	$target_path = $upload_dir . $new_filename;

	// 4.2 一時ファイルを指定のディレクトリに移動
	if (!move_uploaded_file($file_info['tmp_name'], $target_path)) {
		throw new Exception('ファイルの保存中に予期せぬエラーが発生しました。');
	}

	// 4.3 DBに格納するためのファイルパスを用意
	$db_file_path = '../uploads/' . $new_filename;

	// 4.4 DBへのINSERT
	$sql = "INSERT INTO photo (user_id, title, file_path, vote_count) 
            VALUES (:user_id, :title, :file_path, 0)";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindValue(':title', $title, PDO::PARAM_STR);
	$stmt->bindValue(':file_path', $db_file_path, PDO::PARAM_STR);

	$stmt->execute();

	// 4.5 登録完了と同時に写真一覧へ遷移
	unset($_SESSION['upload_error']);
	unset($_SESSION['upload_input']);
	$_SESSION['success_message'] = '写真を投稿しました。';
	header('Location: ../public/list.php');
	exit;
} catch (Exception $e) {
	// データベースエラーが発生した場合
	error_log($e->getMessage());
	$_SESSION['upload_error'] = '写真の投稿処理中にシステムエラーが発生しました。';
	header('Location: ../public/upload.php');
	exit;
}
