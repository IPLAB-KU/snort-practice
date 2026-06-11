<?php

require_once __DIR__ . '/../includes/admin_session.php';
admin_require();
require_once __DIR__ . '/../includes/db_connect.php';

function go($url) { header('Location: ' . $url); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
  go('manage_user.php?error=invalid_id');
}

// 보호: 관리자 계정(users가 아닌 admins 테이블은 다른 곳에서 관리)
// 여기선 일반 사용자(users)만 삭제

// 연관 데이터 정리
$mysqli->query("DELETE FROM cart WHERE user_id = {$id}");
$mysqli->query("DELETE FROM comments WHERE user_id = {$id}");

// 사용자가 작성한 게시글과 그 댓글까지 제거
// 1) 해당 유저 글 목록 조회
$post_ids = [];
if ($res = $mysqli->query("SELECT id FROM posts WHERE user_id = {$id}")) {
  while ($r = $res->fetch_assoc()) $post_ids[] = (int)$r['id'];
  $res->free();
}
if ($post_ids) {
  $in = implode(',', $post_ids);
  $mysqli->query("DELETE FROM comments WHERE post_id IN ({$in})");
  $mysqli->query("DELETE FROM posts WHERE id IN ({$in})");
}

// (선택-권장X) 주문까지 지우려면 아래 주석 해제
// $mysqli->query("DELETE oi FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = {$id}");
// $mysqli->query("DELETE FROM orders WHERE user_id = {$id}");

// 최종: 사용자 삭제
$mysqli->query("DELETE FROM users WHERE id = {$id} LIMIT 1");

if ($mysqli->affected_rows > 0) {
  // (선택) 로그
  // $mysqli->query("INSERT INTO admin_logs (admin_id, action, detail, created_at) VALUES ({$_SESSION['admin']['id']}, 'DELETE_USER', 'user_id={$id}', NOW())");
  go('manage_user.php?msg=deleted');
} else {
  go('manage_user.php?error=delete_failed');
}

?>
