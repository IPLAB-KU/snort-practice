<?php
$page_title = "상품 수정";
$active_page = "product";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("잘못된 접근입니다.");

// 상품 조회
$sql = "SELECT * FROM products WHERE id = {$id}";
$res = $mysqli->query($sql);
$product = $res ? $res->fetch_assoc() : null;
if (!$product) die("상품을 찾을 수 없습니다.");

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $mysqli->real_escape_string($_POST['name']);
  $price = (int)$_POST['price'];
  $stock = (int)$_POST['stock'];
  $desc = $mysqli->real_escape_string($_POST['description']);

  $mysqli->query("UPDATE products SET name='{$name}', price={$price}, stock={$stock}, description='{$desc}' WHERE id={$id}");
  echo "<script>alert('상품이 수정되었습니다.');location.href='manage_product.php';</script>";
  exit;
}
?>

<main class="flex-1 ml-64 p-8">
  <h2 class="text-2xl font-bold mb-6">상품 수정</h2>

  <form method="post" class="bg-white p-6 rounded-lg shadow space-y-4 max-w-2xl">
    <div>
      <label class="block mb-1 font-semibold">상품명</label>
      <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">가격</label>
      <input type="number" name="price" value="<?= (int)$product['price'] ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">재고</label>
      <input type="number" name="stock" value="<?= (int)$product['stock'] ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">설명</label>
      <textarea name="description" rows="4"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">수정 완료</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>