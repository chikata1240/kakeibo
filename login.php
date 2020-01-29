<?php
session_start();
require('dbconnect.php');

// cookieに値が入っていれば、$nameに代入
if ($_COOKIE['name'] !== '') {
  $name = $_COOKIE['name'];
}

// ログインボタンが押されたら処理
if (!empty($_POST)) {
  $name = $_POST['name'];
  // nameが空欄の場合の処理
  if ($_POST['name'] == '') {
    $error['name'] = 'blank';
  }
  // passwordが空欄の場合の処理
  if ($_POST['password'] == '') {
    $error['password'] = 'blank';
  }

  // name passwordが空欄でないかの確認処理
  if (empty($error)) {
    $login = $db->prepare('SELECT * FROM family WHERE name=? AND password=?');
    $login->execute(array(
      $_POST['name'],
      // パスワードは暗号化して送る
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    // データベースから値が$memberに入っていればログインする処理
    if ($member) {
      // セッションの保存
      // セッションにパスワードは保存しない
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      // cookieにセット、有効期限１日
      if ($_POST['save'] === 'on') {
        setcookie('name', $_POST['name'], time() + 60 * 60 * 24);
      }
      // index.phpへ飛ばす
      header('Location:index.php');
      exit();
    } else {
      // データベースから値が$memberに入っていなければエラー処理
      $error['login'] = 'failed';
    }
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/login.css/smart_login_styles.css">
  <link rel="stylesheet" href="css/login.css/tablet_login_styles.css">
  <link rel="stylesheet" href="css/login.css/pc_login_styles.css">
  <title>ログイン</title>
</head>

<body>
  <div class="login_box">
    <div class="login_title">
      <p>ログイン</p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>Name</dt>
        <dd>
          <input type="text" name="name" value="<?php print(htmlspecialchars($name, ENT_QUOTES)); ?>">
          <?php if ($error['name'] === 'blank') : ?>
            <p class="error">入力してください</p>
          <?php endif; ?>
        </dd>
        <dt>Password</dt>
        <dd>
          <input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
          <?php if ($error['password'] === 'blank') : ?>
            <p class="error">入力してください</p>
          <?php endif; ?>
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">Nameを保存する</label>
          <?php if ($error['login'] === 'failed') : ?>
            <p class="error_">ログインに失敗しました</p>
          <?php endif; ?>
        </dd>
      </dl>
      <div class="login_submit">
        <input id="submit_button" type="submit" value="ログインする">
      </div>
    </form>
  </div>
</body>

</html>