<?php
// post_edit.php
$page_title = "게시글 수정 | SW 품질 테스트용 쇼핑몰";

require_once 'includes/session.php';
require_once 'includes/require_user.php'; // 로그인 확인 (세션 기반)
require_once 'includes/db_connect.php';   // $mysqli

include 'includes/header.php';
include 'includes/head.php';

// GET id 체크
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
  echo "<div class='p-8'>잘못된 접근입니다. <a href='board.php'>목록으로</a></div>";
  include __DIR__ . '/includes/footer.php';
  exit;
}

// DB에서 게시글 조회
$sql = "SELECT id, author_user_id, title, content, author_name FROM board_posts WHERE id = {$post_id} LIMIT 1";
$post = null;
if ($res = $mysqli->query($sql)) {
  $post = $res->fetch_assoc();
  $res->free();
}

if (!$post) {
  echo "<div class='p-8'>게시글을 찾을 수 없습니다. <a href='board.php'>목록으로</a></div>";
  include __DIR__ . '/includes/footer.php';
  exit;
}

// 권한 검사: 작성자이거나 관리자만 접근 허용
$login_uid = (int)($_SESSION['user']['id'] ?? 0);
$is_admin  = !empty($_SESSION['user']['is_admin']); // 관리자 여부를 is_admin 플래그로 가정
if ($post['author_user_id'] != $login_uid && !$is_admin) {
  echo "<div class='p-8 text-red-600'>권한이 없습니다. <a href='board.php'>목록으로</a></div>";
  include __DIR__ . '/includes/footer.php';
  exit;
}

// 폼 표시 (기존 title/content 채워서)
?>

<main class="flex justify-center min-h-screen mt-24">
  <div class="w-full max-w-3xl bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold text-center mb-6">게시글 수정</h1>

    <form action="api/post_edit_process.php" method="POST" class="space-y-4">
      <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">

      <div>
        <label for="title" class="block text-sm font-semibold mb-1">제목</label>
        <input type="text" id="title" name="title" required
               value="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="content" class="block text-sm font-semibold mb-1">내용</label>
        <textarea id="content" name="content" rows="10" required
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($post['content']) ?></textarea>
      </div>

      <div class="flex justify-between items-center">
        <a href="post.php?id=<?= (int)$post['id'] ?>"
           class="text-gray-600 hover:text-gray-800 text-sm">
          ← 취소
        </a>
        <div class="flex gap-2">
          <button type="submit"
                  class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600 transition">
            수정 저장
          </button>
          <a href="post.php?id=<?= (int)$post['id'] ?>"
             class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">미리보기</a>
        </div>
      </div>
    </form>
  </div>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();