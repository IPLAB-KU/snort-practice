<!-- admin_login.php -->
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>관리자 로그인 | Vulnerable Shop</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">

  <div class="w-full max-w-sm bg-white shadow-lg border border-gray-200 rounded-lg p-8">
    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">관리자 로그인</h1>

    <?php if (!empty($_GET['error'])): ?>
      <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded px-3 py-2">
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <form action="api/admin_login_process.php" method="POST" class="space-y-4">
      <div>
        <label for="input" class="block text-sm font-semibold text-gray-700 mb-1">이메일</label>
        <input type="input" id="email" name="email" required
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="admin@shop.com" />
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">비밀번호</label>
        <input type="password" id="password" name="password" required
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="비밀번호" />
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
          <span class="text-gray-600">기억하기</span>
        </label>
        <a href="#" class="text-blue-600 hover:underline">비밀번호 찾기</a>
      </div>

      <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        로그인
      </button>
    </form>
  </div>

</body>
</html>