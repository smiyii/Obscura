<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

require_admin();

$stmt = $pdo->query(
    "SELECT id, username, email, role FROM users ORDER BY id"
);
$users = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Admin Panel</h1>
    <p class="page-sub">
      Manage and view registered users
    </p>
  </div>

  <a class="btn btn-accent" href="/dashboard.php">
    Back to Dashboard
  </a>
</div>

<div class="note-grid">
  <div class="note-card">
    <h3>Registered Users</h3>
    <p><?= count($users) ?> total accounts</p>
  </div>

  <div class="note-card">
    <h3>Administrators</h3>
    <p>
      <?= count(array_filter(
          $users,
          fn($u) => $u['role'] === 'admin'
      )) ?> admin accounts
    </p>
  </div>
</div>

<div style="margin-top:32px;">
  <h2 class="page-title">User Directory</h2>
  <p class="page-sub">
    View all registered accounts and their roles.
  </p>

  <div style="overflow-x:auto; margin-top:20px;">
    <table style="
      width:100%;
      border-collapse:collapse;
      color:inherit;
      text-align:left;
    ">
      <thead>
        <tr style="border-bottom:1px solid #444;">
          <th style="padding:16px;">ID</th>
          <th style="padding:16px;">Username</th>
          <th style="padding:16px;">Email</th>
          <th style="padding:16px;">Role</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($users as $user): ?>
          <tr style="border-bottom:1px solid #333;">
            <td style="padding:16px;">
              <?= (int)$user['id'] ?>
            </td>

            <td style="padding:16px;">
              <?= e($user['username']) ?>
            </td>

            <td style="padding:16px;">
              <?= e($user['email']) ?>
            </td>

            <td style="padding:16px;">
              <span class="btn"
                    style="pointer-events:none;">
                <?= e(ucfirst($user['role'])) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
