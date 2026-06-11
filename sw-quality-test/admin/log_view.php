<?php
$page_title = "로그 상세 보기";
$active_page = "log";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php';

// ===== 파라미터 검사 =====
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  die("<main class='ml-64 p-8'><h2 class='text-red-500 font-bold text-xl'>잘못된 접근입니다.</h2></main>");
}

// ===== 로그 데이터 조회 =====
$sql = "SELECT * FROM audit_logs WHERE id = {$id} LIMIT 1";
$res = $mysqli->query($sql);
$log = $res ? $res->fetch_assoc() : null;
if (!$log) {
  die("<main class='ml-64 p-8'><h2 class='text-red-500 font-bold text-xl'>해당 로그를 찾을 수 없습니다.</h2></main>");
}

// 타입 뱃지 색상 처리
$typeBadge = ($log['actor_type'] === 'ADMIN')
  ? '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">ADMIN</span>'
  : '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">USER</span>';
?>

<main class="flex-1 ml-64 p-8 min-h-screen">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">로그 상세 보기</h2>
    <a href="admin_log.php" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition">
      목록으로
    </a>
  </div>

  <div class="bg-white shadow rounded-lg p-6 border border-gray-200 max-w-4xl">
    <table class="min-w-full text-sm">
      <tbody>
        <tr class="border-b">
          <th class="w-40 py-3 px-4 text-left text-gray-600 font-semibold">로그 ID</th>
          <td class="py-3 px-4 text-gray-800">#<?= (int)$log['id'] ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">액터 타입</th>
          <td class="py-3 px-4 text-gray-800"><?= $typeBadge ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">액터 ID</th>
          <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($log['actor_id']) ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">액션</th>
          <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($log['action']) ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold align-top">상세 내용</th>
          <td class="py-3 px-4 text-gray-800 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($log['detail'])) ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">IP 주소</th>
          <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($log['ip']) ?></td>
        </tr>
        <tr class="border-b">
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">User-Agent</th>
          <td class="py-3 px-4 text-gray-800 break-all"><?= htmlspecialchars($log['ua']) ?></td>
        </tr>
        <tr>
          <th class="py-3 px-4 text-left text-gray-600 font-semibold">발생 일시</th>
          <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($log['created_at']) ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-8">
    <a href="log_delete.php?id=<?= (int)$log['id'] ?>"
       onclick="return confirm('정말 이 로그를 삭제하시겠습니까?')"
       class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
      로그 삭제
    </a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>