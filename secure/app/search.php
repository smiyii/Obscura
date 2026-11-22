<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();
$me = current_user();
$q = trim($_GET['q'] ?? '');
$results = [];
if ($q !== '') {
    // SECURE: prepared statement - input can never become SQL
    $stmt = $pdo->prepare("SELECT id, title, content FROM notes WHERE user_id = ? AND (title LIKE ? OR content LIKE ?) ORDER BY updated_at DESC");
    $like = '%' . $q . '%';
    $stmt->execute([$me['id'], $like, $like]);
    $results = $stmt->fetchAll();
}
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1 class="page-title">Search</h1></div>
<form class="search-bar" action="search.php" method="get">
  <input class="form-control form-control-lg" name="q" value="<?= e($q) ?>" placeholder="Search your notes...">
  <button class="btn btn-accent">Search</button>
</form>
<?php if ($q !== ''): ?>
  <p class="page-sub">Results for "<?= e($q) ?>" - <?= count($results) ?> found</p>
  <?php if (!$results): ?><div class="empty"><p>No notes matched.</p></div>
  <?php else: ?>
    <div class="note-grid">
      <?php foreach ($results as $n): ?>
        <a class="note-card" href="notes.php?id=<?= (int)$n['id'] ?>">
          <h3><?= e($n['title']) ?></h3>
          <p><?= e(mb_strimwidth(strip_tags($n['content']), 0, 120, '...')) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>