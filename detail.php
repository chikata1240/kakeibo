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

  $details = $db->prepare('SELECT * FROM family f,living_room l WHERE f.id=l.family_number AND l.purchased_at=? AND l.family_code=? ORDER BY l.Modified ASC');
  $details->execute(array(
    $_REQUEST['action'],
    $family['family_code']
  ));
} else {
  header('Location:login.php');
  exit();
}

$incomes = $db->prepare('SELECT SUM(money) FROM living_room WHERE budget="income" AND purchased_at=? AND family_code=?');
$incomes->execute(array(
  $_REQUEST['action'],
  $family['family_code']
));
$income = $incomes->fetch();
$expenditures = $db->prepare('SELECT SUM(money) FROM living_room WHERE purchased_at=? AND family_code=? AND budget="支出"');
$expenditures->execute(array(
  $_REQUEST['action'],
  $family['family_code']
));
$expenditure = $expenditures->fetch();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/detail.css/smart_detail_styles.css">
  <link rel="stylesheet" href="css/detail.css/tablet_detail_styles.css">
  <link rel="stylesheet" href="css/detail.css/pc_detail_styles.css">
  <title>りれき</title>
</head>

<body>
  <header>
    <a href="index.php">
      <div>
        <p>りれき</p>
      </div>
    </a>
  </header>

  <div class="detail_box">
    <div class="detail_title">
      <?php print($_REQUEST['action']) ?>
    </div>
    <div class="detail_body">
      <?php foreach ($details as $detail) :  ?>

        <div class="detail_boy_box">
          <div class="detail_boy">
            <p><?php print(htmlspecialchars($detail['name'])) ?></p>
            <a href="delete.php?id=<?php print(htmlspecialchars($detail['id'])); ?>">
              <div>
                削除
              </div>
            </a>
          </div>
          <div class="detail_memo_box">
            <p><?php print(htmlspecialchars($detail['budget'])); ?></p>
            <p><?php print(htmlspecialchars($detail['money'])); ?></p>
          </div>
          <p><?php print(htmlspecialchars($detail['memo'])); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="detail_sum_box">
      <p>支出 ¥</p>
      <p><?php print(htmlspecialchars($expenditure['SUM(money)'])); ?></p>
      <p>収入 ¥</p>
      <p><?php print(htmlspecialchars($income['SUM(money)'])); ?></p>
    </div>
  </div>

  <!-- footer -->
  <footer>
    <a href="input.php?action=<?php print($_REQUEST['action']); ?>">
      <div>
        <p>きろくする</p>
      </div>
    </a>
  </footer>
</body>

</html>