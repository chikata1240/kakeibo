<?php
session_start();
require('../dbconnect.php');


// 送信ボタンを押した時にエラーチェックを走らせる
if (!empty($_POST)) {
  // 入力欄のエラーチェック
  if ($_POST['family_code'] === '') {
    $error['family_code'] = 'blank';
  }
  if ($_POST['name'] === '') {
    $error['name'] = 'blank';
  }
  if (strlen($_POST['password']) < 4) {
    $error['password'] = 'length';
  }
  if ($_POST['password'] === '') {
    $error['password'] = 'blank';
  }

  // 重複確認の処理 name
  if (empty($error)) {
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM family WHERE name=?');
    $member->execute(array(
      $_POST['name'],
    ));
    $recond = $member->fetch();
    if ($recond['cnt'] > 0) {
      $error['name'] = 'duplicate';
    }
    // 重複確認の処理 family_name
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM family WHERE  family_code=?');
    $member->execute(array(
      $_POST['family_code']
    ));
    $recond = $member->fetch();
    if ($recond['cnt'] > 4) {
      $error['family_code'] = 'duplicate';
    }
  }

  // エラーがなければcheck.phpへジャンプする
  if (empty($error)) {
    // エラーがなければ$_SESSION['join']に$_POSTを代入する(check.phpに飛ばす)
    $_SESSION['join'] = $_POST;
    header('Location:check.php');
    exit();
  }
} // 送信ボタンを押した時にエラーチェックを走らせる

// check.phpを選択して、ページを戻ってきた場合の処理
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
  // 入力内容を再現
  $_POST = $_SESSION['join'];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/join_css/join_index.css/smart_index_styles.css">
  <link rel="stylesheet" href="../css/join_css/join_index.css/tablet_index_styles.css">
  <link rel="stylesheet" href="../css/join_css/join_index.css/pc_index_styles.css">
  <title>会員登録</title>
</head>

<body>
  <div class="join_index_body_box">
    <div class="join_index_body_title">
      <p>会員登録</p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>Family Code</dt>
        <dd>
          <input type="text" name="family_code" value="<?php print(htmlspecialchars($_POST['family_code'], ENT_QUOTES)); ?>">
          <!-- familyname が空の場合の処理 -->
          <?php if ($error['family_code'] === 'blank') : ?>
            <p class="error">※ファミリーネームを入力してください</p>
          <?php endif; ?>
          <?php if ($error['family_code'] === 'duplicate') : ?>
            <p class="error">※指定されたファミリーネームは、既に登録されています</p>
          <?php endif; ?>
        </dd>
        <dt>Name</dt>
        <dd>
          <input type="text" name="name" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>">
          <!-- name が空の場合の処理 -->
          <?php if ($error['name'] === 'blank') : ?>
            <p class="error">※ニックネームを入力してください</p>
          <?php endif; ?>
          <?php if ($error['name'] === 'duplicate') : ?>
            <p class="error">※指定されたニックネームは、既に登録されています</p>
          <?php endif; ?>
        </dd>
        <dt>
          Password
          <br>
          <span>※４文字以上でご登録ください</span>
        </dt>
        <dd>
          <input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
          <!-- password が空の場合の処理 -->
          <?php if ($error['password'] === 'length') : ?>
            <p class="error">※4文字以上で入力してください</p>
          <?php endif; ?>
          <?php if ($error['password'] === 'blank') : ?>
            <p class="error">※パスワードを入力してください</p>
          <?php endif; ?>
        </dd>
      </dl>
      <div class="entry_submit">
        <input id="submit_buttom" type="submit" value="登録する">
      </div>
    </form>
  </div>
</body>

</html>