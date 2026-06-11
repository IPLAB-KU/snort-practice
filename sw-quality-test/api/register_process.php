<?php
// register_process.php
// 회원가입 처리

// 세션 시작 (이미 시작되어 있으면 재시작하지 않음)
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// DB 연결
require_once __DIR__ . '/../includes/db_connect.php';

// 간단한 유틸
function post($key, $default = '') {
  return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// 1) 입력값 수집
$name             = post('name');
$email            = post('email');
$password         = post('password');
$confirm_password = post('confirm_password');
$phone            = post('phone');
// $birthdate        = post('birthdate');   // YYYY-MM-DD
$address          = post('address');

// 2) 필수 검증
if ($name === '' || $email === '' || $password === '' || $confirm_password === '' ||
    $phone === '' || $address === '') {
  echo "<script>alert('필수 항목이 누락되었습니다.'); history.back();</script>";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "<script>alert('이메일 형식이 올바르지 않습니다.'); history.back();</script>";
  exit;
}

if ($password !== $confirm_password) {
  echo "<script>alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.'); history.back();</script>";
  exit;
}

// 3) SQL 인젝션 대비 최소 이스케이프 (의도적으로 prepare 미사용)
$name_esc      = $mysqli->real_escape_string($name);
$email_esc     = $mysqli->real_escape_string($email);
$pass_esc      = $mysqli->real_escape_string($password);   // 요구사항: 해시 없이 평문 보관
$phone_esc     = $mysqli->real_escape_string($phone);
$birth_esc     = $mysqli->real_escape_string($birthdate);
$address_esc   = $mysqli->real_escape_string($address);

// 4) 이메일 중복 검사
$dupSql = "SELECT id FROM users WHERE email='{$email_esc}' LIMIT 1";
$dupRes = $mysqli->query($dupSql);
if ($dupRes && $dupRes->num_rows > 0) {
  echo "<script>alert('이미 가입된 이메일입니다. 다른 이메일을 사용해주세요.'); history.back();</script>";
  exit;
}
if ($dupRes) $dupRes->free();

// 5) 가입 INSERT
// users 테이블 컬럼 가정: id, name, email, password, phone, birthdate, address, points, status, created_at
$insertSql = "
  INSERT INTO users
    (name, email, password, phone, address, points, status, created_at)
  VALUES
    ('{$name_esc}', '{$email_esc}', '{$pass_esc}', '{$phone_esc}', '{$address_esc}', 100000, 'active', NOW())
";

if (!$mysqli->query($insertSql)) {
  // 실패 시 에러 메시지 확인을 원하면 아래 주석 해제
  // die('회원가입 실패: ' . $mysqli->error);
  echo "<script>alert('회원가입에 실패했습니다. 잠시 후 다시 시도해주세요.'); history.back();</script>";
  exit;
}


// 7) 완료 후 이동 (마이페이지 또는 메인)
echo "<script>alert('회원가입이 완료되었습니다.'); location.href='../login.php';</script>";
exit;

?>
