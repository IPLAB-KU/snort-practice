<?php
$page_title = "상품 상세 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// 1) 파라미터
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2) 상품 조회 (활성 상품만)
$product = null;
if ($id > 0) {
  $sql = "SELECT id, name, price, stock, image_url, description, is_active
          FROM products
          WHERE id = {$id} AND is_active = 1";
  if ($res = $mysqli->query($sql)) {
    $product = $res->fetch_assoc();
    $res->free();
  }
}
?>

<main class="flex flex-col items-center justify-start flex-grow min-h-screen">
  <?php if (!$product): ?>
    <div class="w-full max-w-4xl mt-24 p-6 bg-white rounded-lg shadow min-h-[400px] flex flex-col items-center justify-center">
      <h2 class="text-xl font-bold text-red-600">상품을 찾을 수 없습니다.</h2>
      <p class="mt-2 text-gray-600">잘못된 접근이거나 비활성/삭제된 상품일 수 있습니다.</p>
      <a href="products.php" class="inline-block mt-4 px-4 py-2 bg-slate-700 text-white rounded hover:bg-slate-800">목록으로</a>
    </div>
  <?php else: ?>
    <!-- 상세 레이아웃 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-11/12 md:w-3/4 mt-24 mb-10 bg-white rounded-lg shadow">
      <!-- 이미지 -->
      <div class="p-6">
        <?php if (!empty($product['image_url'])): ?>
          <img class="w-full h-[420px] object-contain bg-gray-50 rounded"
               src="<?= htmlspecialchars($product['image_url']) ?>"
               alt="<?= htmlspecialchars($product['name']) ?>">
        <?php else: ?>
          <div class="w-full h-[420px] flex items-center justify-center bg-gray-100 rounded text-gray-400">No Image</div>
        <?php endif; ?>
      </div>

      <!-- 정보 -->
      <div class="p-6 flex flex-col justify-between">
        <div>
          <h1 class="text-3xl font-bold"><?= htmlspecialchars($product['name']) ?></h1>
          <p class="text-2xl font-bold text-red-500 mt-3">
            <?= number_format((int)$product['price']) ?>원
          </p>
          <p class="mt-2 text-sm <?= ((int)$product['stock']>0?'text-green-600':'text-red-600') ?>">
            재고: <?= (int)$product['stock'] > 0 ? number_format((int)$product['stock']).'개' : '품절' ?>
          </p>
        </div>

        <div class="space-x-2">
          <a href="api/cart_add.php?id=<?= (int)$product['id'] ?>&qty=1<?= (defined('SID')&&SID!==''?'&'.SID:'') ?>"
             class="inline-block bg-orange-500 text-white px-5 py-2 rounded-xl hover:bg-orange-600 <?= (int)$product['stock']>0 ? '' : 'pointer-events-none opacity-50' ?>">
            장바구니 담기
          </a>
          <!--
          <a href="checkout.php?buy_now=1&product_id=<?= (int)$product['id'] ?>&quantity=1"
            class="inline-block bg-blue-600 text-white px-5 py-2 rounded-xl hover:bg-blue-700 <?= (int)$product['stock']>0 ? '' : 'pointer-events-none opacity-50' ?>">
            바로구매
          </a> -->
          <a href="products.php"
             class="inline-block bg-gray-100 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-200">
            목록으로
          </a>
        </div>
      </div>

      <!-- 설명 -->
      <div class="md:col-span-2 p-6">
        <hr class="h-px my-4 border-0 bg-slate-200">
        <h3 class="text-xl font-bold mb-2">상품 설명</h3>
        <div class="prose prose-slate max-w-none">
          <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();
?>