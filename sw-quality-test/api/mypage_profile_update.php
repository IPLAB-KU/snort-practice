<?php
// 세션/유저 검증/DB 연결
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/require_user.php';
require_once __DIR__ . '/../includes/db_connect.php';

$user_id = (int)($_SESSION['user']['id'] ?? 0);

// SID (쿠키 비사용 환경 고려)
$sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';

// POST 값 받기
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');

$current_pw  = trim($_POST['current_password'] ?? '');
$new_pw      = trim($_POST['new_password'] ?? '');
$confirm_pw  = trim($_POST['confirm_password'] ?? '');

if ($user_id <= 0) {
  die("❌ 로그인 정보가 유효하지 않습니다.");
}

if ($name === '' || $email === '') {
  echo "<script>alert('이름과 이메일은 필수 입력 항목입니다.');history.back();</script>";
  exit;
}

// 1) 사용자 정보 수정(비밀번호 제외) (SQL Injection 보호 X)
$escaped_name    = $name;
$escaped_email   = $email;
$escaped_phone   = $phone;
$escaped_address = $address;

$sql_update_profile = "
  UPDATE users
  SET
    name = '{$escaped_name}',
    email = '{$escaped_email}',
    phone = '{$escaped_phone}',
    address = '{$escaped_address}'
  WHERE id = {$user_id}
  LIMIT 1
";

if (!$mysqli->query($sql_update_profile)) {
  die("❌ 프로필 정보 수정 실패: " . htmlspecialchars($mysqli->error));
}


// 2) 비밀번호 변경 처리
if ($new_pw !== '' || $confirm_pw !== '' || $current_pw !== '') {

  if ($new_pw === '' || $confirm_pw === '' || $current_pw === '') {
    echo "<script>alert('비밀번호 변경 시 현재/새/확인 비밀번호를 모두 입력해야 합니다.');history.back();</script>";
    exit;
  }

  if ($new_pw !== $confirm_pw) {
    echo "<script>alert('새 비밀번호와 확인 비밀번호가 일치하지 않습니다.');history.back();</script>";
    exit;
  }

  // 현재 비밀번호 체크
  $esc_cur = $current_pw; // (SQL Injection 보호 X)
  $sql_check = "SELECT id FROM users WHERE id={$user_id} AND password='{$esc_cur}' LIMIT 1";
  $res = $mysqli->query($sql_check);

  if (!$res || $res->num_rows === 0) {
    echo "<script>alert('현재 비밀번호가 일치하지 않습니다.');history.back();</script>";
    exit;
  }

  // 새 비밀번호로 업데이트
  $esc_new = $new_pw; // (SQL Injection 보호 X)
  $sql_pw = "UPDATE users SET password='{$esc_new}' WHERE id={$user_id} LIMIT 1";

  if (!$mysqli->query($sql_pw)) {
    die("❌ 비밀번호 변경 실패: " . htmlspecialchars($mysqli->error));
  }
}


// 수정 완료 후 리디렉션
echo "<script>alert('정보가 정상적으로 수정되었습니다.');location.href='../mypage_profile.php{$sid_q}';</script>";
exit;
