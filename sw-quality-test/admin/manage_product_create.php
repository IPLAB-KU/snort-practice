<?php
$page_title = "상품 등록";
$active_page = "product";
include 'includes/head.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/includes/db_connect.php'; // $mysqli

// ===== 업로드 옵션 =====
//$allowed_ext = ['jpg','jpeg','png','gif','webp']; // (간단 체크)
$max_size_mb = 10; // 참고용(실제 PHP ini upload_max_filesize도 영향)

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name        = isset($_POST['name']) ? $mysqli->real_escape_string($_POST['name']) : '';
  $price       = isset($_POST['price']) ? (int)$_POST['price'] : 0;
  $stock       = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
  $description = isset($_POST['description']) ? $mysqli->real_escape_string($_POST['description']) : '';
  $is_active   = isset($_POST['is_active']) ? 1 : 0;

  // === 이미지 업로드 처리 ===
  $image_url = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      echo "<div class='ml-64 p-8 text-red-600'>❌ 업로드 오류 코드: ".(int)$_FILES['image']['error']."</div>";
      exit;
    }

    $tmp  = $_FILES['image']['tmp_name'];
    $orig = $_FILES['image']['name'];
    $size = (int)$_FILES['image']['size'];

    // 확장자 추출(소문자)
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

    // (선택) 간단 검증
    // if (!in_array($ext, $allowed_ext, true)) {
    //   echo "<div class='ml-64 p-8 text-red-600'>❌ 허용되지 않은 확장자입니다. (허용: ".implode(', ',$allowed_ext).")</div>";
    //   exit;
    // }
    if ($size > $max_size_mb * 1024 * 1024) {
      echo "<div class='ml-64 p-8 text-red-600'>❌ 파일이 너무 큽니다. 최대 {$max_size_mb}MB</div>";
      exit;
    }

    // 저장 폴더: 프로젝트 루트의 product_img/
    // admin/ 에서 한 단계 올라간 루트가 vulnerable-shop/
    $save_dir_abs = realpath(__DIR__ . '/..') . '/product_img';
    // 폴더가 없으면 생성 시도
    if (!is_dir($save_dir_abs)) {
      // 윈도우/리눅스 모두 OK. 실패 시 퍼미션 문제 가능
      if (!mkdir($save_dir_abs, 0777, true)) {
        echo "<div class='ml-64 p-8 text-red-600'>❌ 업로드 폴더 생성 실패: {$save_dir_abs}</div>";
        exit;
      }
    }
    // 최종 쓰기 가능 여부 확인(윈도우에서도 정상 동작)
    if (!is_writable($save_dir_abs)) {
      echo "<div class='ml-64 p-8 text-red-600'>❌ 업로드 폴더에 쓰기 권한이 없습니다: {$save_dir_abs}</div>";
      exit;
    }

    // 저장 파일명
    $new_name = uniqid('prod_', true) . '.' . $ext;

    // 절대경로(실제 저장), 상대경로(DB 저장)
    $dest_abs = $save_dir_abs . '/' . $new_name;
    $dest_rel = 'product_img/' . $new_name;

    // 실제 업로드 수행
    if (!is_uploaded_file($tmp)) {
      echo "<div class='ml-64 p-8 text-red-600'>❌ 업로드된 임시 파일이 아닙니다.</div>";
      exit;
    }
    if (!move_uploaded_file($tmp, $dest_abs)) {
      echo "<div class='ml-64 p-8 text-red-600'>❌ 이미지 저장에 실패했습니다. 경로: ".htmlspecialchars($dest_abs)."</div>";
      exit;
    }

    $image_url = $mysqli->real_escape_string($dest_rel);
  }

  // DB INSERT (prepare 미사용)
  $sql = "
    INSERT INTO products (name, price, stock, image_url, description, is_active, created_at)
    VALUES ('{$name}', {$price}, {$stock}, " . ($image_url !== '' ? "'{$image_url}'" : "NULL") . ", '{$description}', {$is_active}, NOW())
  ";

  if ($mysqli->query($sql)) {
    echo "<script>alert('상품이 등록되었습니다.'); location.href='manage_product.php';</script>";
    exit;
  } else {
    echo "<div class='ml-64 p-8 text-red-600'>❌ 등록 실패: " . htmlspecialchars($mysqli->error) . "</div>";
  }
}
?>

<main class="flex-1 ml-64 p-8">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">상품 등록</h2>
    <a href="manage_product.php" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition">목록</a>
  </div>

  <form method="post" enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg shadow space-y-5 max-w-2xl">
    <div>
      <label class="block mb-1 font-semibold">상품명 <span class="text-red-500">*</span></label>
      <input type="text" name="name" required
             class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-semibold">가격(원) <span class="text-red-500">*</span></label>
        <input type="number" name="price" required min="0"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block mb-1 font-semibold">재고(개) <span class="text-red-500">*</span></label>
        <input type="number" name="stock" required min="0"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
    </div>

    <div>
      <label class="block mb-1 font-semibold">상품 이미지</label>
      <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp"
             class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-blue-600 file:text-white file:rounded hover:file:bg-blue-700">
      <p class="text-xs text-gray-500 mt-1">최대 <?= (int)$max_size_mb ?>MB</p>
    </div>

    <div>
      <label class="block mb-1 font-semibold">상품 설명</label>
      <textarea name="description" rows="6"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
    </div>

    <div class="flex items-center gap-2">
      <input id="is_active" type="checkbox" name="is_active" class="rounded border-gray-300" checked>
      <label for="is_active" class="text-sm text-gray-700">활성화</label>
    </div>

    <div class="pt-2">
      <button type="submit"
              class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        등록하기
      </button>
    </div>
  </form>
</main>

<?php include 'includes/footer.php'; ?>
