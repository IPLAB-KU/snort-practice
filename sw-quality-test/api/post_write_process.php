<?php
require_once '../includes/session.php';
require_once '../includes/require_user.php';
require_once '../includes/db_connect.php';

// 로그인한 사용자 정보
$member_id = (int)($_SESSION['user']['id'] ?? 0);
$author    = $mysqli->real_escape_string($_SESSION['user']['name'] ?? 'guest');

// 폼 데이터 받기
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// 필수값 확인
if ($title === '' || $content === '') {
  echo "<script>alert('제목과 내용을 모두 입력해주세요.');history.back();</script>";
  exit;
}

// DB 저장 (prepare 사용하지 않음 — 취약점 실습 환경 가정)
$title_esc   = $mysqli->real_escape_string($title);
$content_esc = $mysqli->real_escape_string($content);

$sql = "
  INSERT INTO board_posts (author_user_id, title, content, author_name, created_at)
  VALUES ({$member_id}, '{$title_esc}', '{$content_esc}', '{$author}', NOW())
";

if ($mysqli->query($sql)) {
  $post_id = (int)$mysqli->insert_id;

  /* =========================
     ✅ 첨부파일 업로드 + DB 저장
     ========================= */

  // 저장 폴더 (웹루트 기준 상대경로를 DB에 넣을 거라서 이렇게 관리)
  $upload_dir_rel = 'attachment/'; // DB stored_path에 저장할 값(원하면 'attachment/'로 통일해도 됨)
  $upload_dir_abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $upload_dir_rel);

  // 폴더 없으면 생성
  if (!is_dir($upload_dir_abs)) {
    @mkdir($upload_dir_abs, 0777, true);
  }

  // files가 넘어온 경우만 처리
  if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {

    $names  = $_FILES['attachments']['name'];
    $types  = $_FILES['attachments']['type'];
    $tmps   = $_FILES['attachments']['tmp_name'];
    $errors = $_FILES['attachments']['error'];
    $sizes  = $_FILES['attachments']['size'];

    $count = count($names);

    for ($i = 0; $i < $count; $i++) {
      // 빈 파일 입력(선택 안함) 스킵
      if (!isset($errors[$i]) || $errors[$i] === UPLOAD_ERR_NO_FILE) continue;
      if ($errors[$i] !== UPLOAD_ERR_OK) continue;

      $original_name = (string)$names[$i];
      $mime_type     = (string)$types[$i];
      $tmp_path      = (string)$tmps[$i];
      $file_size     = (int)$sizes[$i];

      // (선택) 업로드 제한 - 너무 큰 파일 차단 (예: 10MB)
      // if ($file_size > 10 * 1024 * 1024) continue;

      // 확장자 추출
      $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

      // (선택) 허용 확장자 제한 (원하면 수정)
      // $allowed_ext = ['jpg','jpeg','png','gif','pdf','zip','txt','hwp','doc','docx','xls','xlsx','ppt','pptx'];
      // if ($ext !== '' && !in_array($ext, $allowed_ext, true)) {
      //   // 허용하지 않는 확장자는 스킵 (실습환경이면 여기서 그냥 continue)
      //   continue;
      // }

      // 서버 저장 파일명 생성 (충돌 방지)
      // UUID 대신 간단히: 시간+랜덤 (실습환경이면 이 정도면 충분)
      $rand = bin2hex(random_bytes(8)); // PHP 7+
      $stored_name = date('Ymd_His') . '_' . $rand . ($ext ? ('.' . $ext) : '');

      $dest_path = $upload_dir_abs . DIRECTORY_SEPARATOR . $stored_name;

      // 실제 업로드 이동
      if (!move_uploaded_file($tmp_path, $dest_path)) {
        continue;
      }

      // DB 저장용 이스케이프
      $original_esc = $mysqli->real_escape_string($original_name);
      $stored_esc   = $mysqli->real_escape_string($stored_name);
      $path_esc     = $mysqli->real_escape_string($upload_dir_rel);
      $mime_esc     = $mysqli->real_escape_string($mime_type);

      // 파일 메타 DB 저장
      $sql_file = "
        INSERT INTO board_post_files
          (post_id, original_name, stored_name, stored_path, mime_type, file_size, created_at)
        VALUES
          ({$post_id}, '{$original_esc}', '{$stored_esc}', '{$path_esc}', '{$mime_esc}', {$file_size}, NOW())
      ";
      $mysqli->query($sql_file);
    }
  }

  echo "<script>alert('게시글이 등록되었습니다.');location.href='../post.php?id={$mysqli->insert_id}';</script>";
  exit;
} else {
  error_log('게시글 등록 오류: ' . $mysqli->error);
  echo "<script>alert('게시글 등록 중 오류가 발생했습니다.');history.back();</script>";
  exit;
}
?>