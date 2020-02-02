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

// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
  $ym = $_GET['ym'];
} else {
  // 今月の年月を表示
  $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
  $ym = date('Y-m');
  $timestamp = strtotime($ym . '-01');
}

// 今日の日付 フォーマット　例）2018-07-3
$today = date('Y-m-j');

// カレンダーのタイトルを作成　例）2017年7月
$html_title = date('Y年m月', $timestamp);

// 前月・次月の年月を取得
// 方法１：mktimeを使う mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) - 1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) + 1, 1, date('Y', $timestamp)));
// 方法２：strtotimeを使う
// $prev = date('Y-m', strtotime('-1 month',$timestamp));
// $next = date('Y-m', strtotime('+1 month',$timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か　0:日 1:月 2:火 ... 6:土
// 方法１：mktimeを使う
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));
// 方法２
// $youbi = date('w', $timestamp);

// カレンダー作成の準備
$weeks = [];
$week = '';

// 第１週目：空のセルを追加
// 例）１日が水曜日だった場合、日曜日から火曜日の３つ分の空セルを追加する
$week .= str_repeat('<td></td>', $youbi);

for ($day = 1; $day <= $day_count; $day++, $youbi++) {

  // 2017-07-3
  $date = $ym . '-' . $day;

  if ($today == $date) {
    // 今日の日付の場合は、class="today"をつける
    $week .= '<td class="today">' . "<a href='detail.php?action=$date'>" . "<div>" . $day;
  } else {
    $week .= "<td>" . "<a href='detail.php?action=$date'>" . "<div>" . $day;
  }
  $week .= '</div>' . '</a>' . '</td>';

  // 週終わり、または、月終わりの場合
  if ($youbi % 7 == 6 || $day == $day_count) {

    if ($day == $day_count) {
      // 月の最終日の場合、空セルを追加
      // 例）最終日が木曜日の場合、金・土曜日の空セルを追加
      $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
    }

    // weeks配列にtrと$weekを追加する
    $weeks[] = '<tr>' . $week .  '</tr>';

    // weekをリセット
    $week = '';
  }
}

$this_month = $db->prepare('SELECT SUM(money) FROM living_room WHERE purchased_at LIKE ? AND family_code=? AND budget="支出"');
$this_month->execute(array(
  $ym . "%",
  $family['family_code']
));
$month_expenditure = $this_month->fetch();

$month_peoples = $db->prepare('SELECT f.name, SUM(money) FROM family f, living_room l WHERE f.id=l.family_number AND l.purchased_at LIKE ? AND l.family_code=? AND l.budget="支出" GROUP BY f.name');
$month_peoples->execute(array(
  $ym . "%",
  $family['family_code']
));
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ぽけいぼ</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="css/index.css/smart_index.css">
  <link rel="stylesheet" href="css/index.css/tablet_index.css">
  <link rel="stylesheet" href="css/index.css/pc_index.css">
</head>

<body>
  <!-- header -->
  <header>
    <p>ぽけいぼ</p>
  </header>
  <!-- family name -->
  <div class="family_name">
    <img src="image/kbou.png" class="image_icon" alt="キャラクターのアイコンです" width="50px" height="50px">
    <p><?php print(htmlspecialchars($family['family_code'], ENT_QUOTES)); ?>家</p>
  </div>
  <!-- calendar -->
  <div class="calendar">
    <p class="title"><a href="?ym=<?php echo $prev; ?>">&lt;</a> <?php echo $html_title; ?> <a href="?ym=<?php echo $next; ?>">&gt;</a></p>
    <table class="table table-bordered">
      <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
      </tr>
      <?php
      foreach ($weeks as $week) {
        echo $week;
      }
      ?>
    </table>
  </div>
  <!-- this month -->
  <div class="this_month">
    <div class="expenditure">
      <p>今月の出費： ¥ <?php print(htmlspecialchars($month_expenditure['SUM(money)'])); ?> - </p>
    </div>
    <div class="expenditure_name">
      <?php foreach ($month_peoples as $month_people) : ?>
        <p><?php print(htmlspecialchars($month_people['name'])); ?>： ¥ <?php print(htmlspecialchars($month_people['SUM(money)'])); ?> -</p>
      <?php endforeach; ?>
    </div>
  </div>
  <!-- footer -->
  <footer>
    <a href="input.php?action=<?php print($today); ?>">
      <div>
        <p>今日のきろく</p>
      </div>
    </a>
  </footer>
</body>

</html>