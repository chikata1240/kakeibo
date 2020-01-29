<?php
session_start();
require('dbconnect.php');

// login.phpから移動していない場合、login.phpへ飛ばす
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // sessionのtimeを更新
  $_SESSION['time'] = time();

  $familys = $db->prepare('SELECT * FROM family WHERE id=?');
  $familys->execute(array($_SESSION['id']));
  $family = $familys->fetch();
} else {
  header('Location:login.php');
  exit();
}

if (!empty($_POST)) {
  if ($_POST['money'] !== '') {
    $budget = $db->prepare('INSERT INTO living_room SET family_code=?, family_number=?, budget=?, money=?, memo=?, purchased_at=?');
    $budget->execute(array(
      $family['family_code'],
      $_SESSION['id'],
      $_POST['budget'],
      $_POST['money'],
      $_POST['memo'],
      $_REQUEST['action']
    ));
    header('Location:index.php');
    exit();
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
  <link rel="stylesheet" href="css/input.css/smart_input_styles.css">
  <link rel="stylesheet" href="css/input.css/tablet_input_styles.css">
  <link rel="stylesheet" href="css/input.css/pc_input_styles.css">
  <title>きろく</title>
</head>

<body>
  <header>
    <div>
      <p class="input_title">きろく</p>
    </div>
  </header>

  <div class="input_body">
    <div class="input_body_title_box">
      <p class="input_body_title"><?php print(htmlspecialchars($_REQUEST['action'])); ?></p>
    </div>

    <form action="" method="POST">
      <div class="budget">
        <input id="income" class="radio-inline__input" type="radio" name="budget" value="支出" checked="checked" />
        <label class="radio-inline__label" for="income">
          支出
        </label>
        <input id="expenditure" class="radio-inline__input" type="radio" name="budget" value="収入" />
        <label class="radio-inline__label" for="expenditure">
          収入
        </label>
      </div>
      <br>
      <div class="money_text">
        <label for="money">￥</label>
        <input id="money" type="text" name="money" value="" placeholder="0">
      </div>
      <br>
      <div class="memo_textarea">
        <label for="memo">メモ：</label>
        <textarea id="memo" name="memo" wrap="soft" placeholder="例）食費"></textarea>
      </div>
      <br>
      <div class="submit_box">
        <input class="submit" type="submit" value="送信">
      </div>
    </form>
  </div>

  <!-- footer -->
  <footer>
    <a href="index.php">
      <div>
        <p>ホーム</p>
      </div>
    </a>
  </footer>
</body>

</html>