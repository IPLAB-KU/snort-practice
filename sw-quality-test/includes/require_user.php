<?php
require_once __DIR__ . '/session.php';
if (!is_user_logged_in()) {
  header('Location: /vulnerable-shop/login.php?error='.urlencode('로그인이 필요합니다.'));
  exit;
}