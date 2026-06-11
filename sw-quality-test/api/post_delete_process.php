<?php
// POST: id=<post_id>

require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

// GET 데이터
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
  echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
  exit;
}

// 원본 글 조회
$sql = "SELECT id, author_user_id, title FROM board_posts WHERE id = {$post_id} LIMIT 1";
$post = null;
if ($res = $mysqli->query($sql)) {
  $post = $res->fetch_assoc();
  $res->free();
}

if (!$post) {
  echo "<script>alert('해당 게시글을 찾을 수 없습니다.'); location.href='../board.php';</script>";
  exit;
}

// 권한 검사: 작성자 또는 관리자만 삭제 가능
$login_uid = (int)($_SESSION['user']['id'] ?? 0);
$is_admin  = !empty($_SESSION['user']['is_admin']); // 필요한 경우 세션에 is_admin 필드 사용

if ($post['author_user_id'] != $login_uid && !$is_admin) {
  echo "<script>alert('권한이 없습니다.'); location.href='../post.php?id={$post_id}';</script>";
  exit;
}

// (옵션) 첨부파일이나 연관 레코드가 있으면 여기서 삭제 로직 추가
// 예: order_items, comments, attachments 등. 트랜잭션 고려 가능.

// 삭제 수행
$sql_del = "DELETE FROM board_posts WHERE id = {$post_id} LIMIT 1";
if ($mysqli->query($sql_del)) {
  // 성공: 목록으로 리디렉트
  header("Location: ../board.php");
  exit;
} else {
  error_log("post delete error: " . $mysqli->error);
  echo "<script>alert('게시글 삭제 중 오류가 발생했습니다.'); history.back();</script>";
  exit;
}