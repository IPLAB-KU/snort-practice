<?php
// checkout.php — 장바구니/바로구매 겸용 결제 페이지

// 0) 출력 전에 세션/권한/DB 연결
require_once 'includes/session.php';
require_once 'includes/require_user.php'; // 로그인 필수
require_once 'includes/db_connect.php';

// 세션 쿠키 미사용 환경용 SID
$sid_q   = (defined('SID') && SID !== '') ? ('?' . SID) : '';
$sid_amp = (defined('SID') && SID !== '') ? ('&' . SID) : '';

$page_title = "주문서 작성 | SW 품질 테스트용 쇼핑몰";

// 1) 로그인 사용자
$member_id = (int)$_SESSION['user']['id'];

// 2) 사용자 정보 (DB에서 조회해서 폼에 채우기)
$user = [
  'name'    => '',
  'email'   => '',
  'phone'   => '',
  'address' => '',
  'points'  => 0,
];
$sqlUser = "SELECT name, email, phone, address, points FROM users WHERE id={$member_id} LIMIT 1";
if ($ru = $mysqli->query($sqlUser)) {
  if ($uu = $ru->fetch_assoc()) {
    $user['name']    = $uu['name'] ?? '';
    $user['email']   = $uu['email'] ?? '';
    $user['phone']   = $uu['phone'] ?? '';
    $user['address'] = $uu['address'] ?? '';
    $user['points']  = (int)($uu['points'] ?? 0);
  }
  //$ru->free();
}

// 3) 모드 결정: buy_now(바로구매) vs cart(장바구니)
$mode = (isset($_GET['buy_now']) && (int)$_GET['buy_now'] === 1) ? 'buy_now' : 'cart';

// 4) 결제 대상 아이템 로드
$items = []; // [ ['product_id'=>..,'name'=>..,'price'=>..,'qty'=>..,'image'=>..], ... ]
if ($mode === 'buy_now') {
  $pid = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_GET['product_id'] ?? 0);
  $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
  if ($qty <= 0) $qty = 1;

  if ($pid <= 0) {
    echo "<script>alert('잘못된 상품 요청입니다.'); location.href='products.php{$sid_q}';</script>";
    exit;
  }

  // 단일 상품 조회
  $sqlProd = "SELECT id, name, price, image_url FROM products WHERE id={$pid} LIMIT 1";
  $rp = $mysqli->query($sqlProd);
  if ($rp && $prod = $rp->fetch_assoc()) {
    $items[] = [
      'product_id' => (int)$prod['id'],
      'name'       => $prod['name'] ?? '상품',
      'price'      => (int)($prod['price'] ?? 0),
      'qty'        => $qty,
      'image'      => $prod['image_url'] ?? '',
    ];
  }
  if ($rp) $rp->free();

  if (empty($items)) {
    echo "<script>alert('상품을 찾을 수 없습니다.'); location.href='products.php{$sid_q}';</script>";
    exit;
  }

} else {
  // 장바구니 모드: 회원의 cart + products join
  $sql = "
    SELECT 
      c.product_id,
      c.quantity AS qty,
      p.name,
      p.price,
      p.image_url AS image
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id
    WHERE c.user_id = {$member_id}
    ORDER BY c.id DESC
  ";
  if ($res = $mysqli->query($sql)) {
    while ($row = $res->fetch_assoc()) {
      $items[] = [
        'product_id' => (int)$row['product_id'],
        'name'       => $row['name'] ?? '상품',
        'price'      => (int)($row['price'] ?? 0),
        'qty'        => (int)($row['qty'] ?? 1),
        'image'      => $row['image'] ?? '',
      ];
    }
    $res->free();
  }
  if (empty($items)) {
    echo "<script>alert('장바구니에 담긴 상품이 없습니다.'); location.href='products.php{$sid_q}';</script>";
    exit;
  }
}

// 5) 합계 계산
$subtotal = 0;
foreach ($items as $it) {
  $subtotal += ((int)$it['price']) * ((int)$it['qty']);
}
$shipping = $subtotal > 50000 ? 0 : 3000;
if (empty($items)) $shipping = 0;
$total = $subtotal + $shipping;

// 6) 뷰
include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <form action="api/order_create.php<?= $sid_q ?>" method="post" class="w-full max-w-5xl mt-24 mb-10 grid md:grid-cols-3 gap-6">
    <!-- 주문상품 목록 (간단 요약) -->
    <section class="md:col-span-2 bg-white rounded-lg shadow p-6">
      <h1 class="text-2xl font-bold mb-4">주문 상품</h1>
      <div class="space-y-3">
        <?php foreach ($items as $it): ?>
          <?php $line_total = ((int)$it['price']) * ((int)$it['qty']); ?>
          <div class="flex items-center justify-between border rounded p-3">
            <div class="flex items-center gap-3">
              <?php if (!empty($it['image'])): ?>
                <img src="<?= htmlspecialchars($it['image']) ?>" class="w-16 h-16 object-cover rounded border" alt="">
              <?php else: ?>
                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-gray-400">IMG</div>
              <?php endif; ?>
              <div>
                <div class="font-semibold"><?= htmlspecialchars($it['name']) ?></div>
                <div class="text-sm text-gray-600">수량: <?= (int)$it['qty'] ?></div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm text-gray-600"><?= number_format((int)$it['price']) ?>원</div>
              <div class="font-semibold"><?= number_format($line_total) ?>원</div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- 주문자/배송 정보 -->
      <h2 class="text-2xl font-bold mt-8 mb-4">주문자/배송 정보</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">수령인</label>
          <input name="receiver_name" value="<?php echo $user['name']; ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium">연락처</label>
          <input name="receiver_phone" value="<?php echo $user['phone']; ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">주소</label>
          <input name="receiver_address" value="<?php echo htmlspecialchars($user['address']); ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">배송 요청사항</label>
          <input name="delivery_request" placeholder="문 앞에 놓아주세요" class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <h3 class="text-xl font-bold mt-6 mb-2">결제 수단</h3>
      <div class="space-y-2">
        <label class="flex items-center gap-2"><input type="radio" name="payment_method" value="POINTS" checked> 
          포인트 사용 (보유 포인트: <?= number_format($user['points']) ?>원)
        </label>
      </div>
    </section>

    <!-- 결제 요약 -->
    <aside class="bg-white rounded-lg shadow p-6 h-fit">
      <h3 class="text-xl font-bold mb-4">결제 요약</h3>
      <div class="flex justify-between py-1"><span class="text-gray-600">상품 합계</span><span><?= number_format($subtotal) ?>원</span></div>
      <div class="flex justify-between py-1"><span class="text-gray-600">배송비</span><span><?= number_format($shipping) ?>원</span></div>
      <hr class="my-2">
      <div class="flex justify-between py-1 font-bold text-lg"><span>총 결제금액</span><span><?= number_format($total) ?>원</span></div>

      <!-- order_create.php에서 처리할 히든 필드 -->
      <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
      <input type="hidden" name="total_price" value="<?= (int)$total ?>">

      <?php if ($mode === 'buy_now'): ?>
        <!-- 바로구매 모드일 때는 어떤 상품/수량인지 넘겨줌 -->
        <input type="hidden" name="product_id" value="<?= (int)$items[0]['product_id'] ?>">
        <input type="hidden" name="qty" value="<?= (int)$items[0]['qty'] ?>">
      <?php endif; ?>

      <div class="mt-4">
        <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">결제하기</button>
      </div>
    </aside>
  </form>
</main>
<?php include 'includes/footer.php'; ?>