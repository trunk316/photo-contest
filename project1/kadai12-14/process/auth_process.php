<?php

//ログインのユーザー認証を行う処理

//1.データベース接続,認証
require_once('../app/db.php');
require_once('../app/auth.php');

//2.データ受信
//POSTメソッドで渡されてきたかの確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../public/login.php');
	exit;
}

//以下ログアウト処理
// 処理の種類 (action) を取得
$action = $_POST['action'] ?? '';

if ($action === 'logout') {
	// ログアウト処理
	session_destroy(); // セッションを破棄
	$_SESSION = array();

	// トップページへリダイレクト
	header('Location: ../public/index.php');
	exit;
}

//以下ログイン処理
//受信したログインidとパスワード
$login_id = $_POST['login_id'];
$password = $_POST['password'];

//3.空欄かチェックする
if (empty($login_id) || empty($password)) {
	$_SESSION['error'] = 'IDとパスワードを入力してください。';
	header('Location: ../public/login.php');
	exit;
}

//4.データベースと照合
try {
	//4.1データベースに接続
	$pdo = db_connect();

	// 4.2 DB検索 (login_idからユーザー情報を取得)
	$stmt = $pdo->prepare('SELECT id, login_id, display_name ,password, role FROM user WHERE login_id = :login_id');
	$stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
	$stmt->execute();
	$user = $stmt->fetch();

	// 4.2 パスワード照合
	if ($user && password_verify($password, $user['password'])) {

		// 認証成功: セッションにユーザー情報を保存
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['login_id'] = $user['login_id'];
		$_SESSION['display_name'] = $user['display_name'];
		$_SESSION['user_role'] = $user['role'];
		// エラーメッセージをクリア
		unset($_SESSION['error']);

		// 写真一覧ページへリダイレクト
		header('Location: ../public/list.php');
		exit;
	} else {
		// 認証失敗: エラーメッセージをセッションに保存
		$_SESSION['error'] = 'ログインIDまたはパスワードが間違っています。';
		header('Location: ../public/login.php');
		exit;
	}
} catch (PDOException $e) {
	// データベースエラーが発生した場合
	error_log($e->getMessage());
	$_SESSION['error'] = 'システムエラーが発生しました。';
	header('Location: ../public/login.php');
	exit;
}
