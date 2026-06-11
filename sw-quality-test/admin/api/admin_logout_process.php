<?php
// admin_logout.php
require_once __DIR__ . '/../includes/admin_session.php';

// 관리자 세션만 정리
if (!empty($_SESSION['admin'])) {
  unset($_SESSION['admin']);
}

// (선택) 모든 세션 완전 종료:
// session_unset();
// session_destroy();

// 로그인 페이지로 복귀
header('Location: ../login.php');
exit;