<?php
require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

$comment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post_id    = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($comment_id <= 0 || $post_id <= 0) {
  echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
  exit;
}

// 대상 댓글 조회
$sql = "SELECT id, author_user_id FROM board_comments WHERE id = {$comment_id} LIMIT 1";
$comment = null;
if ($res = $mysqli->query($sql)) {
  $comment = $res->fetch_assoc();
  $res->free();
}
if (!$comment) {
  echo "<script>alert('댓글을 찾을 수 없습니다.'); location.href='../post.php?id={$post_id}';</script>";
  exit;
}

// 권한 체크: 작성자 또는 관리자
$login_uid = (int)($_SESSION['user']['id'] ?? 0);
$is_admin  = !empty($_SESSION['user']['is_admin']);

if ($comment['author_user_id'] != $login_uid && !$is_admin) {
  echo "<script>alert('삭제 권한이 없습니다.'); location.href='../post.php?id={$post_id}';</script>";
  exit;
}

// 삭제
$sqlDel = "DELETE FROM board_comments WHERE id = {$comment_id} LIMIT 1";
if ($mysqli->query($sqlDel)) {
  header("Location: ../post.php?id={$post_id}");
  exit;
} else {
  error_log('comment delete error: ' . $mysqli->error);
  echo "<script>alert('댓글 삭제 중 오류가 발생했습니다.'); history.back();</script>";
  exit;
}