<?php
$page_title = "회원 수정";
$active_page = "user";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("잘못된 접근입니다.");

// 회원 조회
$sql = "SELECT * FROM users WHERE id = {$id}";
$res = $mysqli->query($sql);
$user = $res ? $res->fetch_assoc() : null;
if (!$user) die("회원 정보를 찾을 수 없습니다.");

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $mysqli->real_escape_string($_POST['name']);
  $email = $mysqli->real_escape_string($_POST['email']);
  $points = (int)$_POST['points'];
  $status = $mysqli->real_escape_string($_POST['status']);

  $mysqli->query("UPDATE users SET name='{$name}', email='{$email}', points={$points}, status='{$status}' WHERE id={$id}");
  echo "<script>alert('회원 정보가 수정되었습니다.');location.href='manage_user.php';</script>";
  exit;
}
?>

<main class="flex-1 ml-64 p-8">
  <h2 class="text-2xl font-bold mb-6">회원 정보 수정</h2>

  <form method="post" class="bg-white p-6 rounded-lg shadow space-y-4 max-w-2xl">
    <div>
      <label class="block mb-1 font-semibold">이름</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">이메일</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">포인트</label>
      <input type="number" name="points" value="<?= (int)$user['points'] ?>"
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block mb-1 font-semibold">상태</label>
      <select name="status" class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>active</option>
        <option value="blocked" <?= $user['status'] === 'blocked' ? 'selected' : '' ?>>blocked</option>
      </select>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">수정 완료</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>