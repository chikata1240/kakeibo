<?php
session_start();
require('../dbconnect.php');

// $_SESSION['join']に値が入っているかチェック
if (!isset($_SESSION['join'])) {
  header('Location:index.php');
  exit();
}

// 送信ボタンが押された場合の処理（データベースへの保存）
if (!empty($_POST)) {
  $statement = $db->prepare('INSERT INTO family SET name=?, family_code=?, password=?, created=NOW()');
  $statement->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['family_code'],
    sha1($_SESSION['join']['password'])
  ));
  // セッションを空にする。重複して登録しないようにする
  unset($_SESSION['join']);

  header('Location:thanks.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/join_css/join_check.css/smart_check_styles.css">
  <link rel="stylesheet" href="../css/join_css/join_check.css/tablet_check_styles.css">
  <link rel="stylesheet" href="../css/join_css/join_check.css/pc_check_styles.css">
  <title>会員登録</title>
</head>

<body>
  <div class="join_index_body_box">
    <div class="join_index_body_title">
      <p>会員登録</p>
    </div>
    <p class="body_verification">注意：登録内容はこちらでよろしいでしょうか？</p>
    <form action="" method="post">
      <input type="hidden" name="action" value="submit">
      <dl>
        <dt>Family name</dt>
        <dd>
          <?php print(htmlspecialchars($_SESSION['join']['family_code'], ENT_QUOTES)); ?>
        </dd>
        <dt>Name</dt>
        <dd>
          <?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?>
        </dd>
        <dt>
          Password
        </dt>
        <dd>
          【表示されません】
        </dd>
      </dl>
      <div class="entry_submit">
        <a href="index.php?action=rewrite">編集する</a> ｜ <input type="submit" value="登録する">
      </div>
    </form>
  </div>
</body>

</html>