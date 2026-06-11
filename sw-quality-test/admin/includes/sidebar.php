<?php
require_once __DIR__ . '/admin_session.php'; 
admin_require(); // ✅ 여기서 비관리자 접근 차단 + 로그인 페이지로 이동
?> 

<aside class="fixed left-0 top-0 h-full w-64 bg-gray-800 text-gray-100 flex flex-col">
  <div class="p-6 text-center border-b border-gray-700">
    <a href="index.php">
        <h1 class="text-xl font-bold tracking-wide">Admin Panel</h1>
    </a>
  </div>
  <nav class="flex-1 p-4 space-y-2">
    <a href="manage_product.php"
       class="block px-4 py-2 rounded flex items-center <?= ($active_page === 'product') ? 'bg-gray-700 font-semibold' : 'hover:bg-gray-700 transition' ?>">
       <i class="fas fa-box"></i>  
       <div class="pl-2">상품 관리</div>
    </a>
    <a href="manage_board.php"
       class="block px-4 py-2 rounded flex items-center <?= ($active_page === 'board') ? 'bg-gray-700 font-semibold' : 'hover:bg-gray-700 transition' ?>">
       <i class="fas fa-clipboard"></i>  
       <div class="pl-2">게시글 관리</div>
    </a>
    <a href="manage_user.php"
       class="block px-4 py-2 rounded flex items-center <?= ($active_page === 'user') ? 'bg-gray-700 font-semibold' : 'hover:bg-gray-700 transition' ?>">
       <i class="fas fa-user"></i>  
       <div class="pl-2">회원 관리</div>
    </a>
    <!--
    <a href="log.php"
       class="block px-4 py-2 rounded flex items-center <?= ($active_page === 'log') ? 'bg-gray-700 font-semibold' : 'hover:bg-gray-700 transition' ?>">
       <i class="fas fa-file-alt"></i> 
       <div class="pl-2">로그</div>
    </a> -->
  </nav>

  <!-- ✅ 로그아웃 버튼 추가 -->
  <div class="p-4 border-t border-gray-700">
    <form action="api/admin_logout_process.php" method="post">
      <button type="submit"
              class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded transition">
        <i class="fas fa-sign-out-alt mr-2"></i> 로그아웃
      </button>
    </form>
  </div>

  <!--
  <div class="p-4 border-t border-gray-700 text-center text-sm text-gray-400">
    © 2025 FleaMarket Admin
  </div> -->
</aside>