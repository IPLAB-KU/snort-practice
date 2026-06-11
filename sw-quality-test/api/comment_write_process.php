<?php
require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$content = trim($_POST['content'] ?? '');

if ($post_id <= 0 || $content === '') {
  echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
  exit;
}

// 게시글 존재 확인(옵션이지만 권장)
$sqlPost = "SELECT id FROM board_posts WHERE id = {$post_id} LIMIT 1";
$r = $mysqli->query($sqlPost);
if (!$r || !$r->num_rows) {
  echo "<script>alert('존재하지 않는 게시글입니다.'); history.back();</script>";
  exit;
}
$r->free();

// 작성자 정보
$member_id = (int)($_SESSION['user']['id'] ?? 0);
$author    = $mysqli->real_escape_string($_SESSION['user']['name'] ?? 'guest');

// 저장 (취약점 실습 목적: real_escape_string 사용)
$content_esc = $mysqli->real_escape_string($content);
$sqlIns = "
  INSERT INTO board_comments (post_id, author_user_id, author_name, content, created_at)
  VALUES ({$post_id}, {$member_id}, '{$author}', '{$content_esc}', NOW())
";

if ($mysqli->query($sqlIns)) {
  header("Location: ../post.php?id={$post_id}");
  exit;
} else {
  error_log('comment insert error: ' . $mysqli->error);
  echo "<script>alert('댓글 등록 중 오류가 발생했습니다.'); history.back();</script>";
  exit;
}