<?php

require_once __DIR__ . '/../includes/admin_session.php';
admin_require();
require_once __DIR__ . '/../includes/db_connect.php';

function go($url) { header('Location: ' . $url); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
  go('../manage_board.php?error=invalid_id');
}

// 연관 댓글 제거(없으면 0행 영향)
$mysqli->query("DELETE FROM board_comments WHERE post_id = {$id}");
// 게시글 삭제
$mysqli->query("DELETE FROM board_posts WHERE id = {$id} LIMIT 1");

if ($mysqli->affected_rows > 0) {
  // (선택) 관리자 로그 남기기
  // $mysqli->query("INSERT INTO admin_logs (admin_id, action, detail, created_at) VALUES ({$_SESSION['admin']['id']}, 'DELETE_POST', 'post_id={$id}', NOW())");
  go('../manage_board.php?msg=deleted');
} else {
  go('../manage_board.php?error=delete_failed');
}

?>