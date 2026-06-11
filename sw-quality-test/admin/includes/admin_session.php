<?php
// includes/admin_session.php
// 관리자 전용 접근 제어 (세션 기반, 쿠키 미사용 환경도 고려)

require_once __DIR__ . '/../../includes/session.php'; // 세션 스타트/공용 함수

/**
 * 현재 관리자를 반환
 * - $_SESSION['admin']가 있으면 최우선 사용
 * - 또는 일반 사용자 세션에 is_admin 플래그가 true면 관리자 취급
 */
// function current_admin() {
//     if (!empty($_SESSION['admin']) && !empty($_SESSION['admin']['id'])) {
//         return $_SESSION['admin'];
//     }
//     if (!empty($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
//         return $_SESSION['user'];
//     }
//     return null;
// }

/** 관리자 로그인 여부 */
function admin_logged_in(): bool {
    return current_admin() !== null;
}

/**
 * 관리자 전용 보호 미들웨어
 * - 미로그인/권한없음 시 admin_login.php로 리다이렉트
 * - return_to 파라미터로 원래 페이지 복귀 가능
 * - 쿠키 미사용 세션 환경을 위해 SID 연결도 지원
 */
function admin_require(): void {
    if (admin_logged_in()) return;

    // 현재 요청 URL을 return_to로 전달(간단 인코딩)
    $return_to = '';
    if (!empty($_SERVER['REQUEST_URI'])) {
        $return_to = urlencode($_SERVER['REQUEST_URI']);
    }

    // 세션 ID를 URL로 넘겨야 하는 환경이면 부착
    $sid_q = (defined('SID') && SID !== '') ? ('?' . SID) : '';
    $sep   = ($sid_q === '' ? '?' : '&');

    header('Location: ' . 'login.php' . $sid_q . $sep . 'return_to=' . $return_to);
    exit;
}

/** (옵션) 관리자 로그아웃 도우미 */
function admin_logout(): void {
    if (!empty($_SESSION['admin'])) unset($_SESSION['admin']);
    // 필요 시 일반 사용자 세션도 정리
    // if (!empty($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) unset($_SESSION['user']);
}