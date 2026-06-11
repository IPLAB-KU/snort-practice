<?php
require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

$member_id = (int)$_SESSION['user']['id'];

// POST 데이터 받기
$receiver_name     = trim($_POST['receiver_name'] ?? '');
$receiver_phone    = trim($_POST['receiver_phone'] ?? '');
$receiver_address  = trim($_POST['receiver_address'] ?? '');
$delivery_request  = trim($_POST['delivery_request'] ?? '');
$payment_method    = trim($_POST['payment_method'] ?? 'POINTS');
$total_price       = (int)($_POST['total_price'] ?? 0);

if ($total_price <= 0) {
  die("❌ 유효하지 않은 결제 금액입니다.");
}

// 장바구니 상품 조회
$cart_items = [];
$sql_cart = "
  SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price
  FROM cart c
  JOIN products p ON p.id = c.product_id
  WHERE c.user_id = {$member_id}
";
if ($res = $mysqli->query($sql_cart)) {
  while ($row = $res->fetch_assoc()) $cart_items[] = $row;
  $res->free();
}
if (empty($cart_items)) {
  die("❌ 장바구니가 비어 있습니다.");
}

// 사용자 포인트 조회
$sql_user = "SELECT points FROM users WHERE id={$member_id} LIMIT 1";
$res_user = $mysqli->query($sql_user);
if (!$res_user || !$res_user->num_rows) {
  die("❌ 사용자 정보를 찾을 수 없습니다.");
}
$user = $res_user->fetch_assoc();
$current_points = (int)$user['points'];
$res_user->free();

// 포인트 부족 확인
if ($payment_method === 'POINT' && $current_points < $total_price) {
  die("❌ 포인트가 부족합니다.");
}

// 트랜잭션 시작
$mysqli->begin_transaction();

try {
  // 주문번호 생성
  $order_number = date('YmdHis') . sprintf('%04d', rand(0, 9999));

  // 1️⃣ 주문 생성
  $sql_order = "
    INSERT INTO orders
    (user_id, order_number, total_price, status, payment_method, receiver_name, receiver_phone, receiver_address, delivery_request, created_at)
    VALUES
    ({$member_id}, '{$order_number}', {$total_price}, 'PENDING', '{$mysqli->real_escape_string($payment_method)}',
     '{$mysqli->real_escape_string($receiver_name)}', '{$mysqli->real_escape_string($receiver_phone)}',
     '{$mysqli->real_escape_string($receiver_address)}', '{$mysqli->real_escape_string($delivery_request)}', NOW())
  ";
  if (!$mysqli->query($sql_order)) {
    throw new Exception("주문 생성 실패: " . $mysqli->error);
  }
  $order_id = $mysqli->insert_id;

  // 2️⃣ 주문 아이템 추가
  foreach ($cart_items as $item) {
    $pid = (int)$item['product_id'];
    $pname = $mysqli->real_escape_string($item['name']);
    $qty = (int)$item['quantity'];
    $price = (int)$item['price'];

    $sql_item = "
      INSERT INTO order_items (order_id, product_id, product_name, option_info, quantity, price)
      VALUES ({$order_id}, {$pid}, '{$pname}', '', {$qty}, {$price})
    ";
    if (!$mysqli->query($sql_item)) {
      throw new Exception("주문 상품 추가 실패: " . $mysqli->error);
    }
  }

  // 3️⃣ 포인트 차감
  if ($payment_method === 'POINTS') {
    $sql_update_points = "UPDATE users SET points = points - {$total_price} WHERE id={$member_id}";
    if (!$mysqli->query($sql_update_points)) {
      throw new Exception("포인트 차감 실패: " . $mysqli->error);
    }
  }

  // 4️⃣ 장바구니 비우기
  $sql_clear_cart = "DELETE FROM cart WHERE user_id={$member_id}";
  if (!$mysqli->query($sql_clear_cart)) {
    throw new Exception("장바구니 비우기 실패: " . $mysqli->error);
  }

  // 커밋
  $mysqli->commit();

  // ✅ 주문 완료 페이지로 이동
  header("Location: ../order_complete.php?order_number={$order_number}");
  exit;

} catch (Exception $e) {
  $mysqli->rollback();
  die("❌ 주문 처리 중 오류 발생: " . htmlspecialchars($e->getMessage()));
}

$mysqli->close();
?>