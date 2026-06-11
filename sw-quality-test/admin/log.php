<?php
$page_title = "로그 관리";
$active_page = "log";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// ===== 검색 & 필터 & 페이징 =====
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : ''; // USER / ADMIN / (빈값=전체)
$perPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$where = "WHERE 1";
if ($q !== '') {
  $q_esc = $mysqli->real_escape_string($q);
  $where .= " AND (action LIKE '%{$q_esc}%' OR detail LIKE '%{$q_esc}%' OR ip LIKE '%{$q_esc}%' OR ua LIKE '%{$q_esc}%')";
}
if ($type !== '') {
  $type_esc = $mysqli->real_escape_string($type);
  $where .= " AND actor_type = '{$type_esc}'";
}

// 총 개수
$total = 0;
$sqlCount = "SELECT COUNT(*) AS cnt FROM audit_logs {$where}";
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

// 목록 조회 (최신순)
$sql = "
  SELECT id, actor_type, actor_id, action, detail, ip, ua, created_at
  FROM audit_logs
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
    <h2 class="text-2xl font-bold">로그 관리</h2>
    <form class="flex gap-2" method="get" action="">
      <select name="type"
              class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <option value="" <?= $type===''?'selected':'' ?>>전체 타입</option>
        <option value="USER"  <?= $type==='USER'?'selected':'' ?>>USER</option>
        <option value="ADMIN" <?= $type==='ADMIN'?'selected':'' ?>>ADMIN</option>
      </select>
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="action / detail / IP / UA 검색"
             class="w-72 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
      <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">
        <i class="fas fa-search"></i>
      </button>
    </form>
  </div>

  <p class="text-sm text-gray-500 mb-3">
    총 <strong><?= number_format($total) ?></strong>건 · 페이지 <strong><?= $page ?></strong>/<strong><?= $totalPages ?></strong>
  </p>

  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full border border-gray-200">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">타입</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">액터 ID</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">액션</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">상세</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">IP</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">UA</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">발생 시각</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">관리</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="9" class="px-4 py-6 text-center text-gray-500">표시할 로그가 없습니다.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
            <?php
              $created = $r['created_at'] ? substr($r['created_at'], 0, 19) : '';
              $typeBadge = $r['actor_type'] === 'ADMIN'
                ? '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">ADMIN</span>'
                : '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">USER</span>';
            ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-700">#<?= (int)$r['id'] ?></td>
              <td class="px-4 py-3 text-sm"><?= $typeBadge ?></td>
              <td class="px-4 py-3 text-sm text-gray-700"><?= (int)$r['actor_id'] ?></td>
              <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($r['action']) ?></td>
              <td class="px-4 py-3 text-sm text-gray-700">
                <div class="max-w-xl truncate" title="<?= htmlspecialchars($r['detail']) ?>">
                  <?= htmlspecialchars($r['detail']) ?>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($r['ip']) ?></td>
              <td class="px-4 py-3 text-sm text-gray-700">
                <div class="max-w-xs truncate" title="<?= htmlspecialchars($r['ua']) ?>">
                  <?= htmlspecialchars($r['ua']) ?>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($created) ?></td>
              <td class="px-4 py-3 text-center">
                <a href="log_view.php?id=<?= (int)$r['id'] ?>"
                   class="px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition">보기</a>
                <a href="log_delete.php?id=<?= (int)$r['id'] ?>"
                   onclick="return confirm('해당 로그를 삭제하시겠습니까?')"
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