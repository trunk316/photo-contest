<?php

//アカウントの情報更新、削除をする処理

//1.データベース接続,認証
require_once('../app/db.php');
require_once('../app/auth.php');

require_login();

//2.ユーザーIDの取得
$user_id = $_SESSION['user_id'];

// 3.アクションの取得(更新or削除)
$action = $_POST['action'] ?? '';

try {
	$pdo = db_connect();

	// 4. DB操作
	if ($action === 'modify_execute') {
		//更新の場合
		// 4.1 変更するデータの取得
		$modify_data = $_SESSION['modify_data'] ?? null;
		$new_display_name = $modify_data['display_name'];
		$new_login_id = $modify_data['login_id'];
		$new_password = $modify_data['password'];

		// 4.2　更新用のSQL文に値をセット
		$sql = "UPDATE user SET display_name = :display_name, login_id = :login_id";
		$params = [
			':display_name' => $new_display_name,
			':login_id' => $new_login_id,
			':id' => $user_id
		];

		// パスワードが入力されている場合、ハッシュ化してSQL文に追加
		if (!empty($new_password)) {
			$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
			$sql .= ", password = :password";
			$params[':password'] = $hashed_password;
		}

		$sql .= " WHERE id = :id";

		//　4.3 SQL文を実行
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);

		// 4.4 セッション情報を更新
		$_SESSION['display_name'] = $new_display_name;
		$_SESSION['login_id'] = $new_login_id;

		// 4.5 更新データをクリア
		unset($_SESSION['modify_data']);

		// 4.6 プロフィール画面に遷移
		$_SESSION['success_message'] = 'アカウント情報を更新しました。';
		header('Location: ../public/profile.php');
		exit;
	} elseif ($action === 'delete_execute') {
		//削除の場合
		// 4.1 削除のSQL文を実行
		$stmt = $pdo->prepare('DELETE FROM user WHERE id = :id');
		$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
		$stmt->execute();

		// 4.2 セッション終了、ログアウト
		session_destroy();

		// 4.3 indexページへ遷移
		$_SESSION['success_message'] = 'アカウントを削除しました。';
		header('Location: ../public/index.php'); // トップページへリダイレクト
		exit;
	}
} catch (PDOException $e) {
	error_log($e->getMessage());
	$_SESSION['error'] = '処理中にデータベースエラーが発生しました。';
	header('Location: ../public/profile.php');
	exit;
}
