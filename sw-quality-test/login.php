<?php
$page_title = "Login - SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
?>

<!-- 로그인 폼 -->
<main class="flex items-center justify-center flex-grow bg-gray-50 h-screen">
  <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8 border border-gray-200">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">로그인</h2>

    <form action="api/login_process.php" method="POST" class="space-y-4">
      <!-- 이메일 -->
      <div>
        <label for="input" class="block text-gray-700 mb-1 font-semibold">이메일</label>
        <input type="input" id="email" name="email"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="이메일을 입력하세요" required>
      </div>

      <!-- 비밀번호 -->
      <div>
        <label for="password" class="block text-gray-700 mb-1 font-semibold">비밀번호</label>
        <input type="password" id="password" name="password"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="비밀번호를 입력하세요" required>
      </div>

      <!-- 로그인 버튼 -->
      <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        로그인
      </button>
    </form>

    <!-- 추가 링크 -->
    <div class="text-center mt-4 text-sm text-gray-600">
      <p>계정이 없으신가요?
        <a href="register.php" class="text-blue-600 hover:underline">회원가입</a>
      </p>
      <p class="mt-2">
        <a href="forgot_password.php" class="text-gray-500 hover:underline">비밀번호를 잊으셨나요?</a>
      </p>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>