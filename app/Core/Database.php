<?php
namespace app\Core;

use PDO;

class Database {
  private static $pdo;
  public static function pdo(): PDO {
    if (!self::$pdo) {
      $cfg = require __DIR__ . '/../Config/config.php';
      $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['db_host'], $cfg['db_name']);
      self::$pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    }
    return self::$pdo;
  }
}