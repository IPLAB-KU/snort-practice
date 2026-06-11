<?php
// 1) 세션/권한 체크는 출력 전에!
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/require_user.php';

// 2) DB 연결
require_once __DIR__ . '/includes/db_connect.php';

// 3) 로그인 사용자 정보
$user_id = (int)$_SESSION['user']['id'];
$sql = "SELECT id, name, email, points, created_at, last_login_at FROM users WHERE id={$user_id} LIMIT 1";
$res = $mysqli->query($sql);
$u = $res ? $res->fetch_assoc() : null;

// 4) SID (세션 쿠키 미사용 환경 대응)
$sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';

$page_title = "마이페이지 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex items-center justify-center flex-grow min-h-screen bg-gray-50 py-10">
  <div class="w-full max-w-xl bg-white shadow rounded-lg p-8 border border-gray-200">
    <h2 class="text-2xl font-bold mb-6">마이페이지</h2>

    <?php if ($u): ?>
      <div class="space-y-2 text-gray-800">
        <p><span class="font-semibold">이름:</span> <?= htmlspecialchars($u['name']) ?></p>
        <p><span class="font-semibold">이메일:</span> <?= htmlspecialchars($u['email']) ?></p>
        <p><span class="font-semibold">포인트:</span> <?= number_format((int)$u['points']) ?> P</p>
        <p><span class="font-semibold">가입일:</span> <?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></p>
        <?php if (!empty($u['last_login_at'])): ?>
          <p><span class="font-semibold">마지막 로그인:</span> <?= htmlspecialchars(substr($u['last_login_at'], 0, 19)) ?></p>
        <?php endif; ?>
      </div>

      <div class="mt-6 flex flex-wrap gap-2">
        <a href="api/logout_process.php" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">로그아웃</a>
        <a href="mypage_profile.php<?= $sid_q ?>" class="px-4 py-2 bg-sky-700 text-white rounded hover:bg-sky-800">내 프로필</a>
        <a href="cart.php<?= $sid_q ?>" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">장바구니</a>
        <a href="mypage_orders.php<?= $sid_q ?>" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">주문내역</a>
        <!-- 필요 시 아래 링크를 나중에 구현해서 연결 -->
        <!-- <a href="mypage_edit.php<?= $sid_q ?>" class="px-4 py-2 bg-slate-500 text-white rounded hover:bg-slate-600">정보수정</a> -->
      </div>
    <?php else: ?>
      <p class="text-red-600">사용자 정보를 불러오지 못했습니다.</p>
    <?php endif; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>