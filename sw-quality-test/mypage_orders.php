<?php
// 마이페이지 > 주문내역 리스트

require_once 'includes/session.php';
require_once 'includes/require_user.php'; // 로그인 필수 (경로 주의)
require_once 'includes/db_connect.php';

$page_title = "주문 내역 | SW 품질 테스트용 쇼핑몰";

// 세션 쿠키 미사용 환경이면 URL에 SID 유지
$sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';
$sid_amp = (defined('SID') && SID !== '') ? ('&' . SID) : '';

$member_id = (int)$_SESSION['user']['id'];

// 주문 목록 조회
$orders = [];
$sql = "
  SELECT id, order_number, total_price, status, created_at
  FROM orders
  WHERE user_id = {$member_id}
  ORDER BY id DESC
";
if ($res = $mysqli->query($sql)) {
  while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
  }
  $res->free();
}

include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <div class="w-full max-w-4xl mt-24 mb-10">
    <h1 class="text-2xl font-bold mb-6">주문 내역</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">주문번호</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">주문일시</th>
            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">결제금액</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">상태</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">보기</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                주문 내역이 없습니다.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                  <?= htmlspecialchars($o['order_number']) ?>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  <?= htmlspecialchars(substr($o['created_at'], 0, 16)) ?>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-900">
                  <?= number_format((int)$o['total_price']) ?>원
                </td>
                <td class="px-4 py-3 text-sm">
                  <?= htmlspecialchars($o['status']) ?>
                </td>
                <td class="px-4 py-3 text-center">
                  <!-- 상세 페이지로 이동(둘 중 하나 사용)
                       1) 주문 상세 전용 페이지가 있으면: mypage_order_view.php?id=... 
                       2) 주문 완료/상세 겸용이면: order_complete.php?order_number=...
                  -->
                  <a href="mypage_order_view.php?id=<?= (int)$o['id'] ?><?= $sid_amp ? $sid_amp : '' ?>"
                     class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">
                    상세
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>