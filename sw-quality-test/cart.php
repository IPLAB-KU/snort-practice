<?php
// 장바구니 페이지 (DB 연동)
// - 로그인 필수
// - cart: id, member_id, product_id, quantity
// - products: id, name, price, image_url

// 출력 전에 세션/권한/DB 연결
require_once 'includes/session.php';
require_once 'includes/require_user.php'; // 또는 require_user.php
require_once 'includes/db_connect.php';

$page_title = "장바구니 | SW 품질 테스트용 쇼핑몰";

// 로그인 사용자
$member_id = (int)$_SESSION['user']['id'];

// 장바구니 항목 조회 (JOIN)
$items = [];
$sql = "
  SELECT 
    c.id           AS cart_id,
    c.product_id   AS product_id,
    c.quantity     AS qty,
    p.name         AS name,
    p.price        AS price,
    p.image_url    AS image
  FROM cart c
  LEFT JOIN products p ON p.id = c.product_id
  WHERE c.user_id = {$member_id}
  ORDER BY c.id DESC
";
if ($res = $mysqli->query($sql)) {
  while ($row = $res->fetch_assoc()) {
    $items[] = $row;
  }
  $res->free();
}

// 합계 계산
$subtotal = 0;
foreach ($items as $it) {
  $subtotal += ((int)$it['price']) * ((int)$it['qty']);
}
$shipping = $subtotal > 50000 ? 0 : (count($items) > 0 ? 3000 : 0);
$total    = $subtotal + $shipping;

// 세션 쿠키 미사용 환경의 SID 유지(필요 시)
$sid_q   = (defined('SID') && SID !== '') ? ('?' . SID) : '';
$sid_amp = (defined('SID') && SID !== '') ? ('&' . SID) : '';

include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <div class="w-full max-w-5xl mt-24 mb-10">
    <h1 class="text-2xl font-bold mb-6">장바구니</h1>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="px-4 py-3 text-left">상품</th>
            <th class="px-4 py-3 text-center">수량</th>
            <th class="px-4 py-3 text-right">가격</th>
            <th class="px-4 py-3 text-right">합계</th>
            <th class="px-4 py-3 text-center">관리</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php if (empty($items)): ?>
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-gray-500">장바구니에 담긴 상품이 없습니다.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <?php
                $line_total = ((int)$item['price']) * ((int)$item['qty']);
                $img = trim((string)$item['image']);
              ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <?php if ($img !== ''): ?>
                      <img src="<?= htmlspecialchars($img) ?>" alt="" class="w-16 h-16 object-cover rounded border" />
                    <?php else: ?>
                      <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-gray-400">IMG</div>
                    <?php endif; ?>
                    <div class="font-medium"><?= htmlspecialchars($item['name'] ?? '상품정보없음') ?></div>
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <form action="cart_update.php<?= $sid_q ?>" method="post" class="inline-flex items-center gap-2">
                    <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                    <input type="number" min="1" name="qty" value="<?= (int)$item['qty'] ?>" class="w-16 border rounded px-2 py-1 text-center" readonly>
                    <!-- <button class="px-2 py-1 border rounded hover:bg-gray-100">변경</button> -->
                  </form>
                </td>
                <td class="px-4 py-3 text-right"><?= number_format((int)$item['price']) ?>원</td>
                <td class="px-4 py-3 text-right"><?= number_format($line_total) ?>원</td>
                <td class="px-4 py-3 text-center">
                  <form action="api/cart_remove.php" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                    <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">삭제</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- 합계 -->
    <div class="mt-6 grid md:grid-cols-2 gap-4">
      <div></div>
      <div class="bg-white shadow rounded-lg p-4">
        <div class="flex justify-between py-1">
          <span class="text-gray-600">상품 합계</span><span><?= number_format($subtotal) ?>원</span>
        </div>
        <div class="flex justify-between py-1">
          <span class="text-gray-600">배송비</span><span><?= number_format($shipping) ?>원</span>
        </div>
        <hr class="my-2">
        <div class="flex justify-between py-1 font-bold text-lg">
          <span>총 결제금액</span><span><?= number_format($total) ?>원</span>
        </div>
        <div class="mt-4 flex justify-end">
          <a href="checkout.php<?= $sid_q ?>" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">주문하기</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>