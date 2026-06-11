<?php
// mypage_order_view.php — 주문 상세
// URL 예: mypage_order_view.php?id=123

require_once 'includes/session.php';
require_once 'includes/require_user.php';
require_once 'includes/db_connect.php';

$page_title = "주문 상세 | SW 품질 테스트용 쇼핑몰";

// 세션 쿠키 미사용 환경이라면 SID 유지
$sid_q   = (defined('SID') && SID !== '') ? ('?' . SID) : '';
$sid_amp = (defined('SID') && SID !== '') ? ('&' . SID) : '';

$member_id = (int)($_SESSION['user']['id'] ?? 0);
$order_id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 주문 조회 (본인 주문만)
$order = null;
if ($order_id > 0) {
  $sql = "
    SELECT id, order_number, total_price, status, payment_method,
           receiver_name, receiver_phone, receiver_address, delivery_request,
           created_at
    FROM orders
    WHERE id = {$order_id} AND user_id = {$member_id}
    LIMIT 1
  ";
  if ($res = $mysqli->query($sql)) {
    $order = $res->fetch_assoc();
    $res->free();
  }
}

// 주문 아이템
$items = [];
$subtotal = 0;
if ($order) {
  $oid = (int)$order['id'];
  $sql2 = "
    SELECT product_id, product_name, option_info, quantity, price
    FROM order_items
    WHERE order_id = {$oid}
    ORDER BY id ASC
  ";
  if ($res2 = $mysqli->query($sql2)) {
    while ($r = $res2->fetch_assoc()) {
      $r['quantity']   = (int)$r['quantity'];
      $r['price']      = (int)$r['price'];
      $r['line_total'] = $r['quantity'] * $r['price'];
      $subtotal += $r['line_total'];
      $items[] = $r;
    }
    $res2->free();
  }
}

// 배송비(orders에 별도 컬럼이 없다면 총액-상품합계로 역산)
$shipping = $order ? max(0, (int)$order['total_price'] - (int)$subtotal) : 0;

// 상태 뱃지 클래스
function status_badge_class($status) {
  switch ($status) {
    case '결제완료': return 'bg-green-100 text-green-800';
    case '배송중':   return 'bg-blue-100 text-blue-800';
    case '배송완료': return 'bg-gray-100 text-gray-800';
    case '취소':     return 'bg-red-100 text-red-800';
    default:         return 'bg-slate-100 text-slate-800';
  }
}

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <div class="w-full max-w-5xl mt-24 mb-10">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">주문 상세</h1>
      <a href="mypage_orders.php<?= $sid_q ?>" class="px-3 py-2 border rounded hover:bg-gray-50">← 주문 내역으로</a>
    </div>

    <?php if (!$order): ?>
      <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-xl font-bold text-red-600">주문을 찾을 수 없습니다.</h2>
        <p class="mt-2 text-gray-600">잘못된 접근이거나 다른 계정의 주문일 수 있습니다.</p>
        <a href="mypage_orders.php<?= $sid_q ?>" class="inline-block mt-4 px-4 py-2 bg-slate-700 text-white rounded hover:bg-slate-800">주문 내역</a>
      </div>
    <?php else: ?>
      <!-- 주문 정보 -->
      <section class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <div class="text-sm text-gray-500">주문번호</div>
            <div class="font-semibold"><?= htmlspecialchars($order['order_number']) ?></div>
          </div>
          <div>
            <div class="text-sm text-gray-500">주문일시</div>
            <div class="font-semibold"><?= htmlspecialchars(substr($order['created_at'], 0, 19)) ?></div>
          </div>
          <div>
            <div class="text-sm text-gray-500">주문상태</div>
            <span class="inline-block text-sm px-2 py-1 rounded <?= status_badge_class($order['status']) ?>">
              <?= htmlspecialchars($order['status']) ?>
            </span>
          </div>
          <div>
            <div class="text-sm text-gray-500">결제수단</div>
            <div class="font-semibold">
              <?= $order['payment_method']==='CARD' ? '카드 결제' : ($order['payment_method']==='BANK' ? '무통장 입금' : htmlspecialchars($order['payment_method'])) ?>
            </div>
          </div>
        </div>
      </section>

      <!-- 배송지 -->
      <section class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">배송 정보</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <div class="text-sm text-gray-500">수령인</div>
            <div class="font-semibold"><?= htmlspecialchars($order['receiver_name']) ?></div>
          </div>
          <div>
            <div class="text-sm text-gray-500">연락처</div>
            <div class="font-semibold"><?= htmlspecialchars($order['receiver_phone']) ?></div>
          </div>
          <div class="md:col-span-2">
            <div class="text-sm text-gray-500">주소</div>
            <div class="font-semibold"><?= htmlspecialchars($order['receiver_address']) ?></div>
          </div>
          <?php if (!empty($order['delivery_request'])): ?>
          <div class="md:col-span-2">
            <div class="text-sm text-gray-500">배송 요청사항</div>
            <div class="font-semibold"><?= htmlspecialchars($order['delivery_request']) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- 주문 품목 -->
      <section class="bg-white rounded-lg shadow overflow-x-auto mb-6">
        <table class="min-w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left">상품명</th>
              <th class="px-4 py-3 text-center">수량</th>
              <th class="px-4 py-3 text-right">단가</th>
              <th class="px-4 py-3 text-right">합계</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($items as $it): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <a href="product.php?id=<?= (int)$it['product_id'] ?><?= $sid_amp ?>" class="text-blue-600 hover:underline">
                  <?= htmlspecialchars($it['product_name']) ?>
                </a>
                <?php if (!empty($it['option_info'])): ?>
                  <div class="text-xs text-gray-500"><?= htmlspecialchars($it['option_info']) ?></div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-center"><?= (int)$it['quantity'] ?></td>
              <td class="px-4 py-3 text-right"><?= number_format((int)$it['price']) ?>원</td>
              <td class="px-4 py-3 text-right"><?= number_format((int)$it['line_total']) ?>원</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
              <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">주문 상품이 없습니다.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- 합계 -->
      <section class="bg-white rounded-lg shadow p-6">
        <div class="grid md:grid-cols-2 gap-4">
          <div></div>
          <div class="space-y-2">
            <div class="flex justify-between"><span class="text-gray-600">상품 합계</span><span><?= number_format($subtotal) ?>원</span></div>
            <div class="flex justify-between"><span class="text-gray-600">배송비</span><span><?= number_format($shipping) ?>원</span></div>
            <hr>
            <div class="flex justify-between font-bold text-lg"><span>총 결제금액</span><span><?= number_format((int)$order['total_price']) ?>원</span></div>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>