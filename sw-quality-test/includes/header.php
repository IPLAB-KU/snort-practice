<?php
// includes/header.php
// 헤더: session 포함 -> 로그인 상태에 따라 우측 메뉴 변경

// session.php가 같은 폴더(includes)에 있다고 가정
require_once __DIR__ . '/session.php';

// SID 처리: 세션이 URL 전달 모드일 때 ?PHPSESSID=... 형태로 붙이기 위함
$sid_q = '';
if (defined('SID') && SID !== '') {
  // SID 는 "PHPSESSID=..." 형태 (문자열)임
  $sid_q = '?' . htmlspecialchars(SID);
}

// 현재 로그인 상태
$nav_user  = current_user();   // null or array(id,name,email)
$nav_admin = current_admin();  // null or array(id,name,email)
?>
<header class="fixed w-full top-0 shadow-md z-10">
  <div class="flex justify-between items-center bg-slate-400 p-4 text-white text-center">
    <div>
      <a class="mx-2" href="products.php<?= $sid_q ?>">전체 상품</a>
      <a class="mx-2" href="board.php<?= $sid_q ?>">게시판</a>
    </div>

    <div>
      <i class="fas fa-shopping-bag text-2xl font-bold"></i>
      <span class="ml-2 font-bold text-xl">
        <a href="index.php<?= $sid_q ?>">SW 품질 테스트용 쇼핑몰</a>
      </span>
    </div>

    <div>
      <?php if ($nav_admin): // 관리자로 로그인 되어있을 때 ?>
        <!-- <span class="mr-3">관리자: <strong><?= htmlspecialchars($nav_admin['name']) ?></strong></span> -->
        <a class="mx-2" href="admin/index.php<?= $sid_q ?>">대시보드</a>
        <a class="mx-2" href="admin/api/admin_logout_process.php<?= $sid_q ?>">로그아웃</a>
      <?php elseif ($nav_user): // 일반 사용자로 로그인 되어있을 때 ?>
        <a class="mx-2" href="mypage.php<?= $sid_q ?>">마이페이지</a>
        <a class="mx-2" href="api/logout_process.php<?= $sid_q ?>">로그아웃</a>
      <?php else: // 비로그인 상태 ?>
        <a class="mx-2" href="login.php<?= $sid_q ?>">로그인</a>
        <a class="mx-2" href="register.php<?= $sid_q ?>">회원가입</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- 헤더 아래 여백 확보 -->
<div class="h-20"></div>