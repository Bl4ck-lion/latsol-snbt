<?php
namespace app\Core;

use app\Core\Database;
use PDO;

class Auth {
  public static function login(string $email, string $password): bool {
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1 LIMIT 1');
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($password, $u['password_hash'])) return false;

    // create session
    $sid = bin2hex(random_bytes(32));
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $exp = date('Y-m-d H:i:s', time() + 86400*7); // 7 days
    $pdo->prepare('INSERT INTO sessions (id,user_id,ip,user_agent,expires_at) VALUES (?,?,?,?,?)')
        ->execute([$sid,$u['id'],$ip,$ua,$exp]);
    setcookie('sid', $sid, [
        'expires' => time() + 86400 * 7,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    return true;
  }

  public static function logout() {
      if (isset($_COOKIE['sid'])) {
          $pdo = Database::pdo();
          $st = $pdo->prepare('DELETE FROM sessions WHERE id = ?');
          $st->execute([$_COOKIE['sid']]);
          setcookie('sid', '', -1, '/');
      }
      session_destroy();
  }

  public static function user(): ?array {
    if (empty($_COOKIE['sid'])) return null;
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT u.* FROM sessions s JOIN users u ON u.id=s.user_id WHERE s.id=? AND s.expires_at > NOW() LIMIT 1');
    $st->execute([$_COOKIE['sid']]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function isPremium(?array $user): bool
  {
    if (!$user) {
        return false;
    }
    // 'user' is the default, non-premium role.
    // Any other role is considered premium.
    return in_array($user['role'], ['sepuh', 'moderator', 'admin']);
  }
}