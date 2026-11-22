<?php
require_once __DIR__ . '/helpers.php';
$me = function_exists('current_user') ? current_user() : null;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Obscura</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="assets/css/obscura.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg obscura-nav sticky-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><span class="brand-dot"></span> OBSCURA</a>
    <form class="obscura-search ms-auto me-lg-3" action="search.php" method="get" role="search">
      <input class="form-control" type="search" name="q" placeholder="Search notes..." value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <?php if ($me): ?>
      <div class="d-flex align-items-center gap-2">
        <span class="user-chip"><?= e($me['username']) ?><?= ($me['role'] ?? '') === 'admin' ? ' - admin' : '' ?></span>
        <a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a>
      </div>
    <?php endif; ?>
  </div>
</nav>
<main class="container obscura-main">