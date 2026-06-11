<?php

require_once __DIR__ . '/../includes/admin_session.php';
admin_require();
require_once __DIR__ . '/../includes/db_connect.php';

function go($url) { header('Location: ' . $url); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
  go('../manage_product.php?error=invalid_id');
}

// 이미지 경로 조회
$image_url = '';
$res = $mysqli->query("SELECT image_url FROM products WHERE id = {$id} LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
  $image_url = $row['image_url'] ?? '';
  $res->free();
}

// 카트에서 해당 상품 제거
$mysqli->query("DELETE FROM cart WHERE product_id = {$id}");

// 상품 삭제
$mysqli->query("DELETE FROM products WHERE id = {$id} LIMIT 1");
$ok = $mysqli->affected_rows > 0;

// 이미지 파일 삭제(상품 삭제 성공 시에만)
if ($ok && $image_url) {
  $abs = realpath(__DIR__ . '/..');                 // 프로젝트 루트
  if ($abs) {
    $img_abs = $abs . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $image_url);
    // 파일이 프로젝트 밖으로 나가지 않도록 간단 방어
    if (strpos(realpath(dirname($img_abs)) ?: '', $abs) === 0 && is_file($img_abs)) {
      @unlink($img_abs);
    }
  }
}

if ($ok) {
  // (선택) 로그
  // $mysqli->query("INSERT INTO admin_logs (admin_id, action, detail, created_at) VALUES ({$_SESSION['admin']['id']}, 'DELETE_PRODUCT', 'product_id={$id}', NOW())");
  go('../manage_product.php?msg=deleted');
} else {
  go('../manage_product.php?error=delete_failed');
}

?>