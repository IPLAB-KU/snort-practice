<?php
$page_title = "게시글 수정";
$active_page = "board";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("잘못된 접근입니다.");

// 게시글 조회
$sql = "SELECT * FROM board_posts WHERE id = {$id}";
$res = $mysqli->query($sql);
$post = $res ? $res->fetch_assoc() : null;
if (!$post) die("게시글을 찾을 수 없습니다.");

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $mysqli->real_escape_string($_POST['title']);
  $content = $mysqli->real_escape_string($_POST['content']);

  $mysqli->query("UPDATE board_posts SET title='{$title}', content='{$content}' WHERE id={$id}");
  echo "<script>alert('게시글이 수정되었습니다.');location.href='manage_board.php';</script>";
  exit;
}
?>

<main class="flex-1 ml-64 p-8">
  <h2 class="text-2xl font-bold mb-6">게시글 수정</h2>

  <form method="post" class="bg-white p-6 rounded-lg shadow space-y-4 max-w-3xl">
    <div>
      <label class="block mb-1 font-semibold">제목</label>
      <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">내용</label>
      <textarea name="content" rows="8"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($post['content']) ?></textarea>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">수정 완료</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>