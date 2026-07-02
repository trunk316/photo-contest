<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>写真コンテスト</title>
</head>

<body>

	<h1>写真コンテスト</h1>

	<h2>写真投稿</h2>

	<form method="post" action="../process/upload_process.php" enctype="multipart/form-data">
		<p>写真タイトル
			<input type="text" name="title">
		</p>
		<p>写真アップロード
			<input type="file" name="photo" accept="image/png ,image/jpg">
		</p>

		<button type="submit" name="upload">投稿</button>
	</form>
	<a href="list.php">写真一覧に戻る</a>
</body>

</html>