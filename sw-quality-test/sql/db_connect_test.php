<?php
require_once __DIR__ . '/../includes/db_config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// 에러 처리
if ($mysqli->connect_errno) {
  http_response_code(500);
  die("❌ MySQL 연결 실패 (mysqli): " . $mysqli->connect_error);
}

// 문자셋 설정
$mysqli->set_charset('utf8mb4');
?>

<?php
    echo "DB 연결 성공 (mysqli)";
?>