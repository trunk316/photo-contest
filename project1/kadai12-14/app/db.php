<?php
// DB接続情報の設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_user'); // データベース名
define('DB_USER', 'your_db_password');    // DBユーザー名
define('DB_PASS', 'your_db_name'); // DBパスワード
define('DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4');

function db_connect(): PDO
{
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	];

	try {
		//db接続
		$pdo = new PDO(DSN, DB_USER, DB_PASS, $options);
		return $pdo;
	} catch (PDOException $e) {
		header('Content-Type: text/plain; charset=UTF-8', true, 500);
		// エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
		exit($e->getMessage());
	}
}
