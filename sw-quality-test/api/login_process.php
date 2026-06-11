<?php
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

$email = $_POST['email'] ?? '';
$pass  = $_POST['password'] ?? '';

// SQL Injecttion 가능
$email_esc = $email;
$pass_esc  = $pass;

// 평문 비밀번호 비교 (요청 사양)
$sql = "SELECT id, name, email FROM users WHERE email='{$email_esc}' AND password='{$pass_esc}' LIMIT 1";
$res = $mysqli->query($sql);
$user = $res ? $res->fetch_assoc() : null;

if ($user) {
  $_SESSION['user'] = [
    'id'    => (int)$user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
  ];
  $mysqli->query("UPDATE users SET last_login_at = NOW() WHERE id = ".(int)$user['id']);
  log_audit($mysqli, 'USER', (int)$user['id'], 'LOGIN', 'user login success');
  header('Location: /index.php');
  exit;
} else {
  log_audit($mysqli, 'USER', 0, 'LOGIN_FAIL', "email={$email_esc}");
  header('Location: /login.php?error='.urlencode('이메일/비밀번호를 확인하세요.'));
  exit;
}

?>