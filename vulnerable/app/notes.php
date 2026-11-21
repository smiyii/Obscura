<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();
$me = current_user();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
$stmt->execute([$id]);
$note = $stmt->fetch();
if (!$note) { http_response_code(404); exit('Note not found'); }
$is_owner = ($note['user_id'] == $me['id']);
if (!$is_owner && ($me['role'] ?? '') !== 'admin') { http_response_code(404); exit('Note not found'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?")->execute([$id, $me['id']]);
    header('Location: dashboard.php'); exit;
}
// ===== VULNERABLE: Parsedown safe mode OFF -> scripts in notes run (Stored XSS) =====
require_once __DIR__ . '/assets/lib/Parsedown.php';
$md = new Parsedown();
$body = $md->text($note['content']);
require __DIR__ . '/includes/header.php';
?>
<article class="note-view">
  <div class="page-head">
    <h1 class="page-title"><?= e($note['title']) ?></h1>
    <?php if ($is_owner): ?>
    <div class="d-flex gap-2">
      <a class="btn btn-ghost btn-sm" href="note_edit.php?id=<?= (int)$note['id'] ?>">Edit</a>
      <form method="post" onsubmit="return confirm('Delete this note?')"><input type="hidden" name="action" value="delete"><button class="btn btn-danger btn-sm">Delete</button></form>
    </div>
    <?php endif; ?>
  </div>
  <div class="note-body"><?= $body ?></div>
  <a class="back-link" href="dashboard.php">&larr; Back to notes</a>
</article>
<?php require __DIR__ . '/includes/footer.php'; ?>