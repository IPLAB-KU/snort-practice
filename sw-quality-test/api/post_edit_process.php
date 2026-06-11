<?php
// api/post_edit_process.php
require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

// POST 값 수신
$post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($post_id <= 0 || $title === '' || $content === '') {
  echo "<script>alert('잘못된 요청입니다. 모든 항목을 확인해주세요.'); history.back();</script>";
  exit;
}

// DB에서 원본 글 조회 및 권한 확인
$sql = "SELECT id, author_user_id FROM board_posts WHERE id = {$post_id} LIMIT 1";
$row = null;
if ($res = $mysqli->query($sql)) {
  $row = $res->fetch_assoc();
  $res->free();
}
if (!$row) {
  echo "<script>alert('해당 게시글을 찾을 수 없습니다.'); location.href='/board.php';</script>";
  exit;
}

$login_uid = (int)($_SESSION['user']['id'] ?? 0);
$is_admin  = !empty($_SESSION['user']['is_admin']); // 관리자 여부

if ($row['author_user_id'] != $login_uid && !$is_admin) {
  echo "<script>alert('권한이 없습니다.'); location.href='../board.php';</script>";
  exit;
}

// 업데이트 쿼리
$title_esc   = $mysqli->real_escape_string($title);
$content_esc = $mysqli->real_escape_string($content);

$sql_upd = "
  UPDATE board_posts
  SET title = '{$title_esc}', content = '{$content_esc}', updated_at = NOW()
  WHERE id = {$post_id}
  LIMIT 1
";

if ($mysqli->query($sql_upd)) {
  // 성공 시 수정된 글로 이동
  header("Location: ../post.php?id={$post_id}");
  exit;
} else {
  error_log("post update error: " . $mysqli->error);
  echo "<script>alert('수정 중 오류가 발생했습니다.'); history.back();</script>";
  exit;
}