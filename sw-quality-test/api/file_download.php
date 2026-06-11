<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

$file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
if ($file_id < 1) {
  http_response_code(400);
  exit('Invalid file_id');
}

$sql = "SELECT id, original_name, stored_name, stored_path
        FROM board_post_files
        WHERE id = {$file_id}
        LIMIT 1";
$res = $mysqli->query($sql);
$file = $res ? $res->fetch_assoc() : null;
if ($res) $res->free();

if (!$file) {
  http_response_code(404);
  exit('File not found');
}

// 프로젝트 루트 기준
$project_root = realpath(__DIR__ . '/..');

$rel_path = rtrim($file['stored_path'], "/\\") . DIRECTORY_SEPARATOR . $file['stored_name'];
$abs_path = realpath($project_root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel_path));

// 경로 검증 + 파일 존재 확인
if (!$abs_path || strpos($abs_path, $project_root) !== 0 || !is_file($abs_path)) {
  http_response_code(404);
  exit('File missing on server');
}

// ✅ 실제 파일 크기 사용 (DB값 사용 X)
$size = filesize($abs_path);
if ($size === false) {
  http_response_code(500);
  exit('Cannot read file size');
}

// ✅ MIME 타입을 서버에서 재검출 (브라우저가 준 값 사용 X)
$mime = 'application/octet-stream';
if (function_exists('finfo_open')) {
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  if ($finfo) {
    $detected = finfo_file($finfo, $abs_path);
    if ($detected) $mime = $detected;
    finfo_close($finfo);
  }
}

// 다운로드 카운트(컬럼 있으면 유지, 없으면 이 줄 삭제)
// $mysqli->query("UPDATE board_post_files SET download_count = download_count + 1 WHERE id = {$file_id}");

$original = $file['original_name'];
$original_quoted = rawurlencode($original);

// ✅ 파일 깨짐 방지: 기존 출력 버퍼 싹 비우기
while (ob_get_level()) { ob_end_clean(); }

// 헤더
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Length: ' . $size);
header('Content-Disposition: attachment; filename="' . basename($original) . '"; filename*=UTF-8\'\'' . $original_quoted);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

readfile($abs_path);
exit;