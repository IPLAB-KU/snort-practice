<?php
// 페이지 타이틀 & 공통 헤더
$page_title = "게시글 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli 사용

// 파라미터
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
  $post = null;
} else {
  // 조회수 증가 (실패해도 무시)
  $mysqli->query("UPDATE board_posts SET views = views + 1 WHERE id = {$id}");

  // 게시글 조회
  $sql = "SELECT id, title, content, author_user_id, author_name, views, created_at
          FROM board_posts
          WHERE id = {$id}";
  $res = $mysqli->query($sql);
  $post = $res ? $res->fetch_assoc() : null;
  if ($res) $res->free();

  // 첨부파일 조회
  $files = [];
  if ($post) {
    $sqlF = "SELECT id, post_id, original_name, stored_name, stored_path, mime_type, file_size, created_at
             FROM board_post_files
             WHERE post_id = {$id}
             ORDER BY id ASC";
    if ($resF = $mysqli->query($sqlF)) {
      while ($r = $resF->fetch_assoc()) $files[] = $r;
      $resF->free();
    }
  }

  // 댓글 조회 (오래된 순)
  $comments = [];
  if ($post) {
    $sqlC = "SELECT id, post_id, author_user_id, author_name, content, created_at
             FROM board_comments
             WHERE post_id = {$id}
             ORDER BY id ASC";
    if ($resC = $mysqli->query($sqlC)) {
      while ($r = $resC->fetch_assoc()) $comments[] = $r;
      $resC->free();
    }
  }
}
?>

<main class="flex justify-center flex-grow bg-gray-50">
  <div class="w-full max-w-4xl mt-24 mb-10 bg-white rounded-lg shadow border">

    <?php if (!$post): ?>
      <!-- 없는 글 -->
      <div class="p-8">
        <h2 class="text-xl font-bold text-red-600">게시글을 찾을 수 없습니다.</h2>
        <p class="mt-2 text-gray-600">잘못된 접근이거나 삭제된 게시글일 수 있습니다.</p>
        <a href="board.php" class="inline-block mt-4 px-4 py-2 bg-slate-700 text-white rounded hover:bg-slate-800">목록으로</a>
      </div>
    <?php else: ?>
      <!-- 제목 & 메타 -->
      <header class="px-6 py-5 border-b">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
          <?= htmlspecialchars($post['title']) ?>
        </h1>
        <div class="mt-2 text-sm text-gray-500 flex flex-wrap gap-x-4 gap-y-1">
          <?php
            $authorLabel = $post['author_name'] ?: ('user#' . (int)$post['author_user_id']);
            $created = htmlspecialchars($post['created_at']);
          ?>
          <span>작성자: <span class="text-gray-700"><?= htmlspecialchars($authorLabel) ?></span></span>
          <span>작성일: <time datetime="<?= $created ?>"><?= $created ?></time></span>
          <span>조회수: <?= (int)$post['views'] ?></span>
        </div>

        <!-- 액션(권한 체크는 추후 세션 연동) -->
        <div class="mt-4 flex gap-2">
          <a href="board.php" class="px-3 py-1.5 border rounded hover:bg-gray-50">목록</a>
          <a href="post_edit.php?id=<?= (int)$post['id'] ?>" class="px-3 py-1.5 bg-yellow-500 text-white rounded hover:bg-yellow-600">수정</a>
          <a href="api/post_delete_process.php?id=<?= (int)$post['id'] ?>" class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600"
             onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
        </div>
      </header>

      <!-- 본문 -->
      <section class="px-6 py-6">
        <article class="prose prose-slate max-w-none">
          <?= nl2br($post['content']) ?>
        </article>
        <!-- ✅ 첨부파일 목록 -->
        <div class="mt-6 border-t pt-4">
          <h3 class="text-sm font-semibold text-gray-700">첨부파일</h3>

          <?php if (empty($files)): ?>
            <p class="mt-2 text-sm text-gray-500">첨부파일이 없습니다.</p>
          <?php else: ?>
            <ul class="mt-2 space-y-2">
              <?php foreach ($files as $f): ?>
                <li class="flex items-center justify-between border rounded px-3 py-2 bg-gray-50">
                  <div class="min-w-0">
                    <div class="text-sm text-gray-800 truncate">
                      <?= htmlspecialchars($f['original_name']) ?>
                    </div>
                    <div class="text-xs text-gray-500">
                      <?= number_format((int)$f['file_size']) ?> bytes · <?= htmlspecialchars($f['created_at']) ?>
                    </div>
                  </div>

                  <a
                    class="ml-3 text-sm text-blue-600 hover:underline shrink-0"
                    href="api/file_download.php?file_id=<?= (int)$f['id'] ?>"
                  >
                    다운로드
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </section>

      

      <!-- 댓글 (목록 + 작성) -->
      <section class="px-6 pb-8 border-t">
        <h3 class="text-lg font-semibold mt-6 mb-3">댓글</h3>

        <?php if (empty($comments)): ?>
          <div class="text-sm text-gray-500 mb-3">첫 댓글을 작성해 보세요.</div>
        <?php else: ?>
          <ul class="space-y-3 mb-5">
            <?php foreach ($comments as $c): ?>
              <li class="border rounded p-3 bg-gray-50">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="text-sm text-gray-700">
                      <?= htmlspecialchars($c['author_name'] ?: ('user#' . (int)$c['author_user_id'])) ?>
                    </div>
                    <div class="text-xs text-gray-500"><?= htmlspecialchars($c['created_at']) ?></div>
                  </div>
                  <a href="api/comment_delete_process.php?id=<?= (int)$c['id'] ?>&post_id=<?= (int)$post['id'] ?>"
                     class="text-xs text-red-600 hover:underline"
                     onclick="return confirm('이 댓글을 삭제할까요?')">
                    삭제
                  </a>
                </div>
                <p class="mt-2 text-gray-800"><?= nl2br($c['content']) ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <form action="api/comment_write_process.php" method="post" class="space-y-2">
          <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
          <textarea name="content" rows="3" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                    placeholder="댓글을 입력하세요" required></textarea>
          <div class="flex justify-end">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">댓글 등록</button>
          </div>
        </form>
      </section>

      <!-- 이전/다음글 (간단 네비) -->
      <nav class="py-4 px-6 pb-6 border-t flex justify-between text-sm">
        <a href="post.php?id=<?= max(1, (int)$post['id'] - 1) ?>" class="bg-blue-600 border rounded-full px-3 py-1.5 text-white hover:underline">← 이전글</a>
        <a href="post.php?id=<?= (int)$post['id'] + 1 ?>" class="bg-blue-600 border rounded-full px-3 py-1.5 text-white hover:underline">다음글 →</a>
      </nav>
    <?php endif; ?>

  </div>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();
?>