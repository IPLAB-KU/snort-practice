<?php
$page_title = "주문 완료 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';

$order_number = $_GET['order_number'] ?? '';
?>

<main class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
  <div class="bg-white shadow rounded-lg p-8 text-center max-w-md">
    <h1 class="text-3xl font-bold text-green-600 mb-4">주문이 완료되었습니다!</h1>
    <p class="text-gray-700 mb-6">주문번호: <strong><?= htmlspecialchars($order_number) ?></strong></p>
    <a href="mypage_orders.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">주문 내역 보기</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>