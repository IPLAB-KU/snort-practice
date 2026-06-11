<?php
$page_title = "게시글 작성 | SW 품질 테스트용 쇼핑몰";
include 'includes/header.php';
include 'includes/head.php';
require_once 'includes/db_connect.php'; // DB 연결
?>

<main class="flex justify-center min-h-screen mt-24">
  <div class="w-full max-w-3xl bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold text-center mb-6">게시글 작성</h1>

    <!-- 게시글 작성 폼 -->
    <form action="api/post_write_process.php" method="POST" class="space-y-4" enctype="multipart/form-data">
      <div>
        <label for="title" class="block text-sm font-semibold mb-1">제목</label>
        <input type="text" id="title" name="title" required
               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="content" class="block text-sm font-semibold mb-1">내용</label>
        <textarea id="content" name="content" rows="10" required
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <!-- ✅ 첨부파일 -->
      <div>
        <label for="attachments" class="block text-sm font-semibold mb-1">첨부파일</label>

        <input
          type="file"
          id="attachments"
          name="attachments[]"
          multiple
          class="block w-full text-sm text-gray-700
                 file:mr-4 file:py-2 file:px-4
                 file:rounded file:border-0
                 file:text-sm file:font-semibold
                 file:bg-blue-50 file:text-blue-700
                 hover:file:bg-blue-100
                 border border-gray-300 rounded px-3 py-2"
        >

        <p class="text-xs text-gray-500 mt-1">
          여러 파일 선택 가능 (Shift/Ctrl). 파일 크기 제한은 서버 설정(PHP upload_max_filesize)을 따릅니다.
        </p>
      </div>

      <div class="flex justify-between items-center">
        <a href="board.php"
           class="text-gray-600 hover:text-gray-800 text-sm">
          ← 목록으로
        </a>
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
          등록하기
        </button>
      </div>
    </form>
  </div>
</main>

<?php
include 'includes/footer.php';
$mysqli->close();
?>