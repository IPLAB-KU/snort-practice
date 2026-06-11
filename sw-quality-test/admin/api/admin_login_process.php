<?php
// admin_login_process.php
// POST: email, password  (remember 체크박스는 무시: 세션만 사용)

require_once '../includes/admin_session.php'; // session_start 포함
require_once '../includes/db_connect.php';    // $mysqli

// 에러 리다이렉트 도우미
function back_with_error($msg) {
  $err = urlencode($msg);
  header("Location: ../login.php?error={$err}");
  exit;
}

// 입력값
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// 필수 체크
if ($email === '' || $password === '') {
  back_with_error('이메일과 비밀번호를 모두 입력하세요.');
}

// 관리자 조회 SQL Injection 보호 X
$email_esc = $email;
$password_esc = $password;
$sql = "SELECT id, name, email, password FROM admins WHERE email = '{$email_esc}' AND password = '{$password_esc}' LIMIT 1";
$res = $mysqli->query($sql);
if (!$res || $res->num_rows === 0) {
  back_with_error('존재하지 않는 계정입니다.');
}
$admin = $res->fetch_assoc();
$res->free();

// 평문 비밀번호 비교 (실서비스라면 password_hash/verify 사용)
// if ($password !== (string)$admin['password']) {
//   back_with_error('비밀번호가 올바르지 않습니다.');
// }

// 세션에 관리자 저장
$_SESSION['admin'] = [
  'id'       => (int)$admin['id'],
  'name'     => $admin['name'],
  'email'    => $admin['email'],
  'is_admin' => true,
];

// (선택) 일반 사용자 세션과도 동기화하고 싶다면:
// $_SESSION['user'] = ['id' => (int)$admin['id'], 'name' => $admin['name'], 'email' => $admin['email'], 'is_admin' => true];

// 로그인 성공 → 대시보드로
header('Location: ../index.php');
exit;