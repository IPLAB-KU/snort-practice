<?php
// 0) 출력 전에 세션/권한/DB 연결
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/require_user.php';
require_once __DIR__ . '/includes/db_connect.php';

// 1) 로그인 유저 id
$user_id = (int)($_SESSION['user']['id'] ?? 0);

// 2) DB에서 사용자 프로필 조회
$sql = "
  SELECT
    id,
    name,
    email,
    COALESCE(phone, '')   AS phone,
    COALESCE(address, '') AS address,
    created_at,
    last_login_at
  FROM users
  WHERE id = {$user_id}
  LIMIT 1
";
$profile = null;
if ($res = $mysqli->query($sql)) {
  $profile = $res->fetch_assoc();
  $res->free();
}

// 3) (쿠키 미사용 환경이면) SID 유지
$sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';

// 4) 헤드/헤더는 이 뒤에
$page_title = "내 정보 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <div class="w-full max-w-3xl mt-24 mb-10 bg-white rounded-lg shadow p-8">
    <h1 class="text-2xl font-bold mb-6">내 정보</h1>

    <?php if ($profile): ?>
      <form action="api/mypage_profile_update.php<?= $sid_q ?>" method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">이름</label>
          <input name="name" value="<?= htmlspecialchars($profile['name']) ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium">이메일</label>
          <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium">전화번호</label>
          <input name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium">주소</label>
          <input name="address" value="<?= htmlspecialchars($profile['address']) ?>" class="w-full border rounded px-3 py-2">
        </div>

        <hr class="my-4">

        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium">현재 비밀번호</label>
            <input type="password" name="current_password" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium">새 비밀번호</label>
            <input type="password" name="new_password" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium">새 비밀번호 확인</label>
            <input type="password" name="confirm_password" class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="flex justify-end">
          <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">저장</button>
        </div>
      </form>
    <?php else: ?>
      <p class="text-red-600">사용자 정보를 불러오지 못했습니다.</p>
    <?php endif; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>