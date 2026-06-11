<?php
$page_title = "SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex flex-col items-center justify-center h-screen text-center bg-gray-50">
    <div class="max-w-3xl p-8 bg-white rounded-lg shadow-lg">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to SW Quality Test Shop 🛍️</h1>
        <p class="text-lg text-gray-600 mb-4">
            이 사이트는 <strong>소프트웨어 품질 테스트용 웹 쇼핑몰</strong>입니다.
        </p>
        <p class="text-gray-600 mb-4">
            PHP, MySQL, TailwindCSS를 기반으로 제작되었으며,
            실제 서비스용이 아닌 <strong>소프트웨어 품질 테스트</strong>를 목적으로 합니다.
        </p>
        <!--
        <p class="text-gray-500 italic">
            ⚠️ 관리자 계정, SQL 인젝션, XSS, CSRF 등 다양한 취약점 실습이 가능합니다.
        </p>
        -->
        <a href="products.php" class="mt-6 inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition">
            전체 상품 보기
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>