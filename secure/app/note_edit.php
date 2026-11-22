<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();
$me = current_user();
$id = (int)($_GET['id'] ?? 0);
$note = ['title' => '', 'content' => ''];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $me['id']]);
    $note = $stmt->fetch();
    if (!$note) { http_response_code(404); exit('Note not found'); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    if ($title === '') { $error = 'Title cannot be empty.'; }
    elseif ($id) {
        $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?")->execute([$title, $content, $id, $me['id']]);
        header("Location: notes.php?id=$id"); exit;
    } else {
        $pdo->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)")->execute([$me['id'], $title, $content]);
        header('Location: notes.php?id=' . $pdo->lastInsertId()); exit;
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1 class="page-title"><?= $id ? 'Edit note' : 'New note' ?></h1></div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="note-form">
  <input class="form-control form-control-lg" name="title" value="<?= e($note['title']) ?>" placeholder="Title" autofocus>
    <textarea class="form-control" name="content" rows="14" placeholder="Write your note..."><?= e($note['content']) ?></textarea>
  <div class="d-flex gap-2"><button class="btn btn-accent">Save</button><a class="btn btn-ghost" href="dashboard.php">Cancel</a></div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>