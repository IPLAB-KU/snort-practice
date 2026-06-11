<?php
// includes/pagination.php
// 페이지네이션 컴포넌트
// 필수 변수: $page, $totalPages, page_qs($p)

if (!function_exists('page_qs')) {
  function page_qs($p) {
    $q = $_GET;
    $q['page'] = (int)$p;
    return '?' . http_build_query($q);
  }
}
?>

<?php if ($totalPages > 1): ?>
  <div class="flex justify-center my-6">
    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
      <!-- Prev -->
      <?php $prevDisabled = ($page <= 1); ?>
      <a href="<?= $prevDisabled ? '#' : page_qs($page-1) ?>"
         class="px-3 py-1 border text-sm rounded-l-md <?= $prevDisabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50' ?>">
        Prev
      </a>

      <!-- Page numbers (현재 페이지 기준 ±2) -->
      <?php
        $start = max(1, $page - 2);
        $end   = min($totalPages, $page + 2);
        if ($start > 1) echo '<span class="px-3 py-1 border bg-white">…</span>';
        for ($p = $start; $p <= $end; $p++):
      ?>
        <a href="<?= page_qs($p) ?>"
           class="px-3 py-1 border text-sm <?= $p==$page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50' ?>">
          <?= $p ?>
        </a>
      <?php endfor;
        if ($end < $totalPages) echo '<span class="px-3 py-1 border bg-white">…</span>';
      ?>

      <!-- Next -->
      <?php $nextDisabled = ($page >= $totalPages); ?>
      <a href="<?= $nextDisabled ? '#' : page_qs($page+1) ?>"
         class="px-3 py-1 border text-sm rounded-r-md <?= $nextDisabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50' ?>">
        Next
      </a>
    </nav>
  </div>
<?php endif; ?>