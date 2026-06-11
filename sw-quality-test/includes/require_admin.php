<?php
require_once __DIR__ . '/session.php';
if (!is_admin_logged_in()) {
  header('Location: /admin_login.php?error='.urlencode('관리자 로그인이 필요합니다.'));
  exit;
}