<?php
$page_title = "전체 상품 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';

// DB 연결 (includes/db_connect.php 안에서 $mysqli 생성)
require_once __DIR__ . '/includes/db_connect.php';

// ===== 페이징 변수 (안전하게 정수 캐스트) =====
$perPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 총 개수 조회 (간단 쿼리)
$total = 0;
$resCount = $mysqli->query("SELECT COUNT(*) AS cnt FROM products WHERE is_active=1");
if ($resCount) {
    $row = $resCount->fetch_assoc();
    $total = (int)$row['cnt'];
    $resCount->free();
}

// 총 페이지
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$limit  = (int)$perPage;
$offset = (int)$offset;

// 상품 목록 쿼리 (prepared 사용하지 않음 — 정수 캐스트 적용)
$sql = "SELECT id, name, price, image_url, description
        FROM products
        WHERE is_active = 1
        ORDER BY id DESC
        LIMIT {$limit} OFFSET {$offset}";

$products = [];
if ($result = $mysqli->query($sql)) {
    while ($r = $result->fetch_assoc()) {
        $products[] = $r;
    }
    $result->free();
}

// 쿼리스트링 유지하며 page만 바꾸는 함수
function page_qs($p) {
    $q = $_GET;
    $q['page'] = (int)$p;
    return '?' . http_build_query($q);
}
?>

<main class="flex flex-col flex-grow">
  <div class="px-4">
    <h1 class="text-2xl font-bold mt-2 mb-4">전체 상품</h1>
    <p class="text-sm text-gray-500 mb-2">
      총 <strong><?= number_format($total) ?></strong>개 상품 · 페이지 <strong><?= htmlspecialchars($page) ?></strong>/<strong><?= htmlspecialchars($totalPages) ?></strong>
    </p>
  </div>

  <?php if (empty($products)): ?>
    <div class="p-6 text-center text-gray-600">표시할 상품이 없습니다.</div>
  <?php else: ?>
    <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
      <?php foreach ($products as $p): ?>
        <div class="border p-4 rounded shadow hover:shadow-lg transition bg-white">
          <?php if (!empty($p['image_url'])): ?>
            <a href="product.php?id=<?= (int)$p['id'] ?>">
              <img class="w-full h-56 mb-3 object-cover rounded bg-gray-100"
                   src="<?= htmlspecialchars($p['image_url']) ?>"
                   alt="<?= htmlspecialchars($p['name']) ?>">
            </a>
          <?php else: ?>
            <a href="product.php?id=<?= (int)$p['id'] ?>">
              <div class="w-full h-56 mb-3 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                No Image
              </div>
            </a>
          <?php endif; ?>

          <a href="product.php?id=<?= (int)$p['id'] ?>">
            <h3 class="font-bold mb-1 line-clamp-1"><?= htmlspecialchars($p['name']) ?></h3>
          </a>
          <p class="text-sm text-gray-600 mb-3 line-clamp-2">
            <?= htmlspecialchars(mb_strimwidth($p['description'] ?? '', 0, 90, '…', 'UTF-8')) ?>
          </p>

          <div class="flex items-center justify-between">
            <a href="api/cart_add.php?id=<?= (int)$p['id'] ?>"
               class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition text-sm">
              장바구니
            </a>
            <div class="text-xl font-bold text-gray-800">
              <?= number_format((int)$p['price']) ?>원
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </section>

    <!-- 페이지네이션 -->
    <?php
      // ✅ 페이지네이션 컴포넌트 include
      include __DIR__ . '/includes/pagination.php';
    ?>
  <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
// DB 연결 닫기
$mysqli->close();
?>