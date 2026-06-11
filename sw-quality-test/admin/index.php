<?php
$page_title = "대시보드";
$active_page = "dashboard";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/../includes/db_connect.php'; // DB 연결

// --- 상품 수 ---
$sql_products = "SELECT COUNT(*) AS cnt FROM products";
$res_products = $mysqli->query($sql_products);
$product_count = ($res_products && $row = $res_products->fetch_assoc()) ? (int)$row['cnt'] : 0;

// --- 주문 수 ---
$sql_orders = "SELECT COUNT(*) AS cnt FROM orders";
$res_orders = $mysqli->query($sql_orders);
$order_count = ($res_orders && $row = $res_orders->fetch_assoc()) ? (int)$row['cnt'] : 0;

// --- 회원 수 ---
$sql_users = "SELECT COUNT(*) AS cnt FROM users";
$res_users = $mysqli->query($sql_users);
$user_count = ($res_users && $row = $res_users->fetch_assoc()) ? (int)$row['cnt'] : 0;

$mysqli->close();
?>

<main class="flex-1 ml-64 p-8 bg-gray-50 min-h-screen">
  <h2 class="text-2xl font-bold mb-6">대시보드</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:shadow-lg transition">
      <h3 class="font-semibold text-lg text-gray-700">총 상품 수</h3>
      <p class="text-3xl font-bold text-blue-600 mt-2"><?= number_format($product_count) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:shadow-lg transition">
      <h3 class="font-semibold text-lg text-gray-700">총 주문 수</h3>
      <p class="text-3xl font-bold text-green-600 mt-2"><?= number_format($order_count) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:shadow-lg transition">
      <h3 class="font-semibold text-lg text-gray-700">총 회원 수</h3>
      <p class="text-3xl font-bold text-purple-600 mt-2"><?= number_format($user_count) ?></p>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>