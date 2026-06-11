<?php
// // 세션 쿠키 미사용 버전
// // PHP 기본 세션 쿠키 기능 비활성화
// ini_set('session.use_cookies', 0);
// ini_set('session.use_only_cookies', 0);
// ini_set('session.use_trans_sid', 1); // URL 기반 세션 ID 허용

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 세션 헬퍼
function current_user() { return $_SESSION['user'] ?? null; }
//function current_admin(){ return $_SESSION['admin'] ?? null; }
function current_admin() {
    if (!empty($_SESSION['admin']) && !empty($_SESSION['admin']['id'])) {
        return $_SESSION['admin'];
    }
    if (!empty($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
        return $_SESSION['user'];
    }
    return null;
}
function is_user_logged_in(){ return isset($_SESSION['user']); }
function is_admin_logged_in(){ return isset($_SESSION['admin']); }

// function session_reissue(){
//     if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
// }

// 감사 로그 기록 (간단 버전)
function log_audit($mysqli, $actor_type, $actor_id, $action, $detail=''){
  $actor_type_esc = $mysqli->real_escape_string($actor_type);
  $actor_id_int   = (int)$actor_id;
  $action_esc     = $mysqli->real_escape_string($action);
  $detail_esc     = $mysqli->real_escape_string($detail);
  $ip_esc = $mysqli->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
  $ua_esc = $mysqli->real_escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
  $mysqli->query("
    INSERT INTO audit_logs (actor_type, actor_id, action, detail, ip, ua, created_at)
    VALUES ('{$actor_type_esc}', {$actor_id_int}, '{$action_esc}', '{$detail_esc}', '{$ip_esc}', '{$ua_esc}', NOW())
  ");
}
?>