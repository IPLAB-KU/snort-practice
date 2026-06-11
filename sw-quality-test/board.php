<?php
$page_title = "게시판 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// ===== 페이징 설정 =====
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 총 게시글 수
$total = 0;
if ($res = $mysqli->query("SELECT COUNT(*) AS cnt FROM board_posts")) {
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
  ORDER BY id DESC
  LIMIT {$limit} OFFSET {$offset}
";
$posts = [];
if ($res = $mysqli->query($sql)) {
  while ($r = $res->fetch_assoc()) $posts[] = $r;
  $res->free();
}
?>

<main class="flex flex-row justify-center min-h-screen mt-10">
  <div class="w-full max-w-7xl bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold text-center py-6">게시판</h1>
    <a href="post_write.php"
         class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
        글 작성
    </a>

    <table class="w-full border-t mt-4">
      <thead>
        <tr class="border-b bg-gray-50">
          <th class="px-4 py-2 text-center font-semibold">번호</th>
          <th class="px-4 py-2 text-center font-semibold">제목</th>
          <th class="px-4 py-2 text-center font-semibold">작성자</th>
          <th class="px-4 py-2 text-center font-semibold">작성일</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($posts)): ?>
          <tr>
            <td colspan="4" class="px-4 py-6 text-center text-gray-600">표시할 게시글이 없습니다.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($posts as $idx => $post): ?>
            <?php
              // 번호: 전체 최신순 기준 역순 번호(선택)
              $rownum = $total - $offset - $idx;
              $author = $post['author_name'] ?: ('user#' . (int)$post['author_user_id']);
              $date   = substr($post['created_at'] ?? '', 0, 10);
            ?>
            <tr class="border-b hover:bg-gray-100 cursor-pointer"
                onclick="location.href='post.php?id=<?= (int)$post['id'] ?>'">
              <td class="px-4 py-2 text-center"><?= $rownum ?></td>
              <td class="px-4 py-2 text-left">
                <a class="hover:underline" href="post.php?id=<?= (int)$post['id'] ?>">
                  <?= htmlspecialchars($post['title']) ?>
                </a>
              </td>
              <td class="px-4 py-2 text-center"><?= htmlspecialchars($author) ?></td>
              <td class="px-4 py-2 text-center"><?= htmlspecialchars($date) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <?php
      // 페이지네이션 컴포넌트 (필수 변수: $page, $totalPages)
      include __DIR__ . '/includes/pagination.php';
    ?>
  </div>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();
?>