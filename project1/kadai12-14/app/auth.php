<?php

require_once('db.php');

//ログインチェックなどの共通ロジック

//1.DBに接続
$pdo = db_connect();

// 2. セッションの開始
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

//3.ログイン状態のチェック
function is_logged_in(): bool
{
	// セッションにユーザーIDが存在するかどうかで判定
	return isset($_SESSION['user_id']);
}

//4.ロールの確認
function get_role(): ?string
{
	if (is_logged_in()) {
		//セッションに保存されたロールを返す　ない場合はNULL
		return $_SESSION['user_role'] ?? null;
	}
	return null;
}

//5.ログインできていなければ強制的にログイン画面に遷移
function require_login(): void
{
	if (!is_logged_in()) {
		//ログインが切れていたらログイン画面へ
		header('Location: ../public/login.php');
		exit;
	}
}
