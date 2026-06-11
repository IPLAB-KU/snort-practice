<?php
// cart_add.php

require_once '../includes/session.php';
require_once '../includes/require_user.php'; // 로그인 필수
require_once '../includes/db_connect.php';   // $mysqli

// 세션ID(URL 세션 유지) 붙임
$sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';
$sid_amp = (defined('SID') && SID !== '') ? ('&' . SID) : '';

// 1) 파라미터
$pid = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_GET['product_id'] ?? 0);
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : (isset($_POST['qty']) ? (int)$_POST['qty'] : 1);
if ($qty <= 0) $qty = 1;

if ($pid <= 0) {
  // 잘못된 접근 — 상품페이지로 보내거나 이전페이지로
  echo "<script>alert('잘못된 상품 요청입니다.'); history.back();</script>";
  exit;
}

$member_id = (int)$_SESSION['user']['id'];

// 2) 상품 존재 확인
$sqlProd = "SELECT id, name FROM products WHERE id = {$pid} LIMIT 1";
$res = $mysqli->query($sqlProd);
if (!$res) {
  echo "<script>alert('DB 오류가 발생했습니다.'); history.back();</script>";
  exit;
}
$product = $res->fetch_assoc();
$res->free();

if (!$product) {
  echo "<script>alert('존재하지 않는 상품입니다.'); history.back();</script>";
  exit;
}

// 3) 장바구니에 기존 항목 있는지 확인
$sqlFind = "SELECT id, quantity FROM cart WHERE user_id = {$member_id} AND product_id = {$pid} LIMIT 1";
$res2 = $mysqli->query($sqlFind);
if (!$res2) {
  echo "<script>alert('DB 오류가 발생했습니다.'); history.back();</script>";
  exit;
}

if ($row = $res2->fetch_assoc()) {
  // 3-1) 기존 수량 업데이트
  $newQty = (int)$row['quantity'] + $qty;
  $cartId = (int)$row['id'];
  $upd = "UPDATE cart SET quantity = {$newQty} WHERE id = {$cartId}";
  if (!$mysqli->query($upd)) {
    echo "<script>alert('장바구니 업데이트에 실패했습니다.'); history.back();</script>";
    exit;
  }
  // 성공 → cart.php로 이동 (메시지 파라미터 선택)
  header('Location: ../cart.php' . ($sid_q ? $sid_q . '&' : '?') . 'msg=updated');
  exit;

} else {
  // 3-2) 새 항목 추가
  $ins = "INSERT INTO cart (user_id, product_id, quantity) VALUES ({$member_id}, {$pid}, {$qty})";
  if (!$mysqli->query($ins)) {
    echo "<script>alert('장바구니 추가에 실패했습니다.'); history.back();</script>";
    exit;
  }
  // 성공 → cart.php로 이동
  header('Location: ../cart.php' . ($sid_q ? $sid_q . '&' : '?') . 'msg=added');
  exit;
}

?>