<?php
$page_title = "회원 관리";
$active_page = "user";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// ===== 검색 & 페이징 =====
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$where = "WHERE 1";
if ($q !== '') {
  $q_esc = $q; // SQL Injection 방지 X
  $where .= " AND (name LIKE '%{$q_esc}%' OR email LIKE '%{$q_esc}%')";
}

// 총 개수
$total = 0;
$sqlCount = "SELECT COUNT(*) AS cnt FROM users {$where}";
if ($res = $mysqli->query($sqlCount)) {
  $row = $res->fetch_assoc();
  $total = (int)$row['cnt'];
  $res->free();
}

$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;

$offset = ($page - 1) * $perPage;
$limit  = (int)$perPage;
$offset = (int)$offset;

// 목록 조회 (최신 가입 순)
$sql = "
  SELECT id, name, email, points, status, created_at
  FROM users
  {$where}
  ORDER BY id DESC
  LIMIT {$limit} OFFSET {$offset}
";
$rows = [];
if ($res = $mysqli->query($sql)) {
  while ($r = $res->fetch_assoc()) $rows[] = $r;
  $res->free();
}

// 페이지네이션용 보조 함수(없으면 정의)
if (!function_exists('page_qs')) {
  function page_qs($p) {
    $q = $_GET;
    $q['page'] = (int)$p;
    return '?' . http_build_query($q);
  }
}
?>

<main class="flex-1 ml-64 p-8">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">회원 관리</h2>
    <form class="flex gap-2" method="get" action="">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="이름 또는 이메일 검색"
             class="w-64 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
      <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">
        <i class="fas fa-search"></i>
      </button>
    </form>
  </div>

  <p class="text-sm text-gray-500 mb-3">
    총 <strong><?= number_format($total) ?></strong>명 · 페이지 <strong><?= $page ?></strong>/<strong><?= $totalPages ?></strong>
  </p>

  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full border border-gray-200">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">회원 ID</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">이름</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">이메일</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">포인트</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">상태</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">가입일</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">관리</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">표시할 회원이 없습니다.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
            <?php
              $created = $r['created_at'] ? substr($r['created_at'], 0, 10) : '';
              $statusBadge = $r['status'] === 'active'
                ? '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">active</span>'
                : '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">blocked</span>';
            ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-700">#<?= (int)$r['id'] ?></td>
              <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($r['name']) ?></td>
              <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($r['email']) ?></td>
              <td class="px-4 py-3 text-sm text-gray-700"><?= number_format((int)$r['points']) ?></td>
              <td class="px-4 py-3 text-sm"><?= $statusBadge ?></td>
              <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($created) ?></td>
              <td class="px-4 py-3 text-center">
                <a href="manage_user_edit.php?id=<?= (int)$r['id'] ?>"
                   class="px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">수정</a>
                <a href="api/manage_user_delete.php?id=<?= (int)$r['id'] ?>"
                   onclick="return confirm('해당 회원을 삭제하시겠습니까? (주의: 관련 데이터는 별도 정리 필요)')"
                   class="px-2 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition ml-2">삭제</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php include __DIR__ . '/includes/pagination.php'; ?>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();
?>