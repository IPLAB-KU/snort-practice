<?php
// cart_remove.php — 장바구니 상품 삭제 처리

require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

$member_id = (int)($_SESSION['user']['id'] ?? 0);
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// 유효성 검사
if ($member_id <= 0 || $product_id <= 0) {
  http_response_code(400);
  echo "<script>alert('잘못된 요청입니다.');history.back();</script>";
  exit;
}

// cart 테이블에서 삭제
$sql = "DELETE FROM cart WHERE user_id = {$member_id} AND product_id = {$product_id} LIMIT 1";
if ($mysqli->query($sql)) {
  // 성공 시 장바구니 페이지로 이동
  header("Location: ../cart.php");
  exit;
} else {
  // DB 오류 시
  error_log("Cart remove failed: " . $mysqli->error);
  echo "<script>alert('삭제 중 오류가 발생했습니다.');history.back();</script>";
  exit;
}
?>