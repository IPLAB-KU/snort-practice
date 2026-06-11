<?php
$page_title = "회원가입 | Vulnerable Shop";
include 'includes/header.php';
include 'includes/head.php';
?>

<!-- 회원가입 폼 -->
<main class="flex items-center justify-center flex-grow bg-gray-50 min-h-screen py-10">
  <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8 border border-gray-200">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">회원가입</h2>

    <form action="api/register_process.php" method="POST" class="space-y-4">
      <!-- 이름 -->
      <div>
        <label for="name" class="block text-gray-700 mb-1 font-semibold">이름</label>
        <input type="text" id="name" name="name"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="이름을 입력하세요" required>
      </div>

      <!-- 이메일 -->
      <div>
        <label for="email" class="block text-gray-700 mb-1 font-semibold">이메일</label>
        <input type="email" id="email" name="email"
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

      <!-- 비밀번호 확인 -->
      <div>
        <label for="confirm_password" class="block text-gray-700 mb-1 font-semibold">비밀번호 확인</label>
        <input type="password" id="confirm_password" name="confirm_password"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="비밀번호를 다시 입력하세요" required>
      </div>

      <!-- 전화번호 -->
      <div>
        <label for="phone" class="block text-gray-700 mb-1 font-semibold">전화번호</label>
        <input type="text" id="phone" name="phone"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="예: 010-1234-5678" required>
      </div>

      <!-- 생년월일 -->
      <!--
      <div>
        <label for="birthdate" class="block text-gray-700 mb-1 font-semibold">생년월일</label>
        <input type="date" id="birthdate" name="birthdate"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               required>
      </div> -->

      <!-- 주소 -->
      <div>
        <label for="address" class="block text-gray-700 mb-1 font-semibold">주소</label>
        <input type="text" id="address" name="address"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
               placeholder="주소를 입력하세요" required>
      </div>

      <!-- 가입 버튼 -->
      <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        회원가입
      </button>
    </form>

    <!-- 추가 링크 -->
    <div class="text-center mt-4 text-sm text-gray-600">
      <p>이미 계정이 있으신가요?
        <a href="login.php" class="text-blue-600 hover:underline">로그인</a>
      </p>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>