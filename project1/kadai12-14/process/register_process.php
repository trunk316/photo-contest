<?php

//アカウントを新しく登録する処理

//1.データベース接続,認証
require_once('../app/db.php');
require_once('../app/auth.php');

//2.データ受信
//POSTメソッドで渡されてきたかの確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../public/register.php');
	exit;
}

// 受信した登録情報
$login_id = $_POST['login_id'] ?? '';
$display_name = $_POST['display_name'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'viewer'; // デフォルトロールは 'viewer' 

//3.空欄かチェックする
if (empty($login_id) || empty($display_name) || empty($password)) {
	$_SESSION['register_error'] = 'すべての項目を入力してください。';
	header('Location: ../public/register.php');
	exit;
}

//4.DBに登録する
try {
	$pdo = db_connect();

	// 4.1 ログインIDの重複チェック
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE login_id = :login_id');
	$stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
	$stmt->execute();

	if ($stmt->fetchColumn() > 0) {
		// IDがすでに存在する場合
		$_SESSION['register_error'] = 'このログインIDはすでに使用されています。別のIDをお試しください。';
		header('Location: ../public/register.php');
		exit;
	}

	// 4.2 パスワードのハッシュ化
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);

	// 4.3 DBへのINSERT処理
	$sql = "INSERT INTO user (login_id, display_name,  password, role) 
            VALUES (:login_id, :display_name, :password, :role)";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
	$stmt->bindValue(':display_name', $display_name, PDO::PARAM_STR);
	$stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
	$stmt->bindValue(':role', $role, PDO::PARAM_STR);

	$stmt->execute();

	// 4.4 登録完了と同時にlogin画面へ遷移
	$_SESSION['success_message'] = 'アカウント登録が完了しました。ログインしてください。';
	header('Location: ../public/login.php');
	exit;
} catch (PDOException $e) {
	// データベースエラーが発生した場合
	error_log($e->getMessage());
	$_SESSION['error'] = 'システムエラーが発生しました。';
	header('Location: ../public/register.php');
	exit;
}
