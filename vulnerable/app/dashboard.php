<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();
$me = current_user();
$stmt = $pdo->prepare("SELECT id, title, content, updated_at FROM notes WHERE user_id = ? ORDER BY updated_at DESC");
$stmt->execute([$me['id']]);
$notes = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <div>
    <h1 class="page-title">Your notes</h1>
    <p class="page-sub"><?= count($notes) ?> note<?= count($notes) === 1 ? '' : 's' ?></p>
  </div>
  <a class="btn btn-accent" href="note_edit.php">+ New note</a>
</div>
<?php if (!$notes): ?>
  <div class="empty"><p>No notes yet.</p><a class="btn btn-accent" href="note_edit.php">Create your first note</a></div>
<?php else: ?>
  <div class="note-grid">
    <?php foreach ($notes as $n): ?>
      <a class="note-card" href="notes.php?id=<?= (int)$n['id'] ?>">
        <h3><?= e($n['title']) ?></h3>
        <p><?= e(mb_strimwidth(strip_tags($n['content']), 0, 120, '...')) ?></p>
        <span class="note-date"><?= e(date('d M Y', strtotime($n['updated_at']))) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>