<?php
// 例外処理
try {
  $ini = parse_ini_file('db.ini', false);
  $db = new PDO('mysql:host=' . $ini['host'] . ';dbname=' . $ini['dbname'] . ';charset=utf8', $ini['dbusr'], $ini['dbpass']);
} catch (PDOException $e) {
  print('DB接続エラー：' . $e->getMessage());
}
