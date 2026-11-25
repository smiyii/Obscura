<?php
require_once __DIR__ . '/helpers.php';

$me = function_exists('current_user') ? current_user() : null;

if ($me) {
    $_SESSION['logout_token'] ??= bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Obscura</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="assets/css/obscura.css"
        rel="stylesheet"
    >
</head>

<body>

<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg obscura-nav sticky-top">
    <div class="container">

        <a class="navbar-brand" href="/dashboard.php">
            <span class="brand-dot"></span>
            OBSCURA
        </a>

        <form
            class="obscura-search ms-auto me-lg-3"
            action="/search.php"
            method="get"
            role="search"
        >
            <input
                class="form-control"
                type="search"
                name="q"
                placeholder="Search notes..."
                value="<?= e($_GET['q'] ?? '') ?>"
            >
        </form>

        <?php if ($me): ?>

            <div class="d-flex align-items-center gap-2">

                <!-- CURRENT USER -->
                <span class="user-chip">
                    <?= e($me['username']) ?>

                    <?php if (($me['role'] ?? '') === 'admin'): ?>
                        - admin
                    <?php endif; ?>
                </span>

                <!-- ADMIN PANEL -->
                <?php if (($me['role'] ?? '') === 'admin'): ?>
                    <a
                        class="btn btn-sm btn-outline-light"
                        href="/admin.php"
                    >
                        Admin Panel
                    </a>
                <?php endif; ?>

                <!-- LOGOUT POPUP BUTTON -->
                <button
                    type="button"
                    class="btn btn-sm btn-outline-light"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal"
                >
                    Logout
                </button>

            </div>

        <?php endif; ?>

    </div>
</nav>


<!-- LOGOUT CONFIRMATION MODAL -->
<?php if ($me): ?>

<div
    class="modal fade"
    id="logoutModal"
    tabindex="-1"
    aria-labelledby="logoutModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div
            class="modal-content"
            style="
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: 22px;
            "
        >

            <!-- MODAL HEADER -->
            <div class="modal-header border-0">

                <h2
                    class="modal-title fs-4"
                    id="logoutModalLabel"
                >
                    Log out of Obscura?
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>

            <!-- MODAL BODY -->
            <div
                class="modal-body"
                style="color: var(--muted);"
            >
                Are you sure you want to end your session?
            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-ghost"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <form
                    action="/logout.php"
                    method="POST"
                    class="m-0"
                >

                    <input
                        type="hidden"
                        name="token"
                        value="<?= htmlspecialchars(
                            $_SESSION['logout_token'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <button
                        type="submit"
                        class="btn btn-accent"
                    >
                        Log out
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

<?php endif; ?>


<!-- BOOTSTRAP JAVASCRIPT -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    defer
></script>


<!-- MAIN CONTENT -->
<main class="container obscura-main">
