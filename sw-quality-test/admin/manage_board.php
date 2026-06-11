<?php
$page_title = "게시글 관리";
$active_page = "board";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// ===== 검색 & 페이징 =====
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// where 절
$where = "WHERE 1";
if ($q !== '') {
  $q_esc = $q; // SQL Injecton 방지 X
  $where .= " AND title LIKE '%{$q_esc}%'";
}

// 총 개수
$total = 0;
$sqlCount = "SELECT COUNT(*) AS cnt FROM board_posts {$where}";
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
  SELECT id, title, author_user_id, author_name, created_at
  FROM board_posts
  {$where}
  ORDER BY id DESC
  LIMIT {$limit} OFFSET {$offset}
";
$rows = [];
if ($res = $mysqli->query($sql)) {
  while ($r = $res->fetch_assoc()) $rows[] = $r;
  $res->free();
}

// 페이지네이션용 page_qs (없으면 정의)
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
    <h2 class="text-2xl font-bold">게시글 관리</h2>
    <!-- Add New Post Button 
    <a href="post_write.php"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      + 새 글 작성
    </a>
    -->
  </div>

  <div class="mb-6">
    <form class="flex gap-2" method="get" action="">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="게시글 제목 검색"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
      <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">
        <i class="fas fa-search"></i>
      </button>
    </form>
    <p class="text-sm text-gray-500 mt-2">
      총 <strong><?= number_format($total) ?></strong>건 · 페이지 <strong><?= $page ?></strong>/<strong><?= $totalPages ?></strong>
    </p>
  </div>

  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full border border-gray-200">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">번호</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">제목</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">작성자</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">작성일</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">관리</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">표시할 게시글이 없습니다.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $idx => $r): ?>
            <?php
              $rownum = $total - $offset - $idx; // 전체 기준 역순 번호
              $author = $r['author_name'] ?: ('user#' . (int)$r['author_user_id']);
              $created = $r['created_at'] ? substr($r['created_at'], 0, 10) : '';
            ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-700"><?= $rownum ?></td>
              <td class="px-4 py-3 text-sm text-gray-900">
                <a href="../post.php?id=<?= (int)$r['id'] ?>" class="hover:underline">
                  <?= htmlspecialchars($r['title']) ?>
                </a>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($author) ?></td>
              <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($created) ?></td>
              <td class="px-4 py-3 text-center">
                <a href="manage_post_edit.php?id=<?= (int)$r['id'] ?>"
                   class="px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">수정</a>
                <a href="api/manage_post_delete.php?id=<?= (int)$r['id'] ?>"
                   onclick="return confirm('해당 게시글을 삭제하시겠습니까?')"
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