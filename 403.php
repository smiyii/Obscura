<?php
http_response_code(403);
$homeUrl = '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>403 | Access Forbidden</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh; display: flex; align-items: center;
            justify-content: center; background: #0b0f19; color: #fff;
            font-family: Arial, Helvetica, sans-serif; text-align: center; padding: 20px;
        }
        .container { max-width: 600px; width: 100%; animation: fadeIn .8s ease; }
        .error-code {
            font-size: clamp(110px, 22vw, 200px); font-weight: 800;
            letter-spacing: -10px; line-height: 1;
            background: linear-gradient(135deg, #38bdf8, #6366f1, #a855f7);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        h1 { font-size: clamp(26px, 5vw, 38px); margin: 20px 0 15px; }
        p { font-size: 16px; color: #94a3b8; line-height: 1.8; margin-bottom: 35px; }
        .btn {
            display: inline-block; padding: 14px 32px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff; text-decoration: none; font-size: 15px;
            font-weight: 600; border-radius: 10px; transition: .3s ease;
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(99,102,241,.3); }
        .icon { margin-bottom: 20px; }
        .icon svg { width: 65px; height: 65px; stroke: #818cf8; }
        .footer { margin-top: 60px; font-size: 12px; color: #475569; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 480px) {
            .error-code { letter-spacing: -5px; }
            p { font-size: 14px; }
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6-7V7a6 6 0 1112 0v3m-14 0h16v11H4V10z" />
            </svg>
        </div>
        <div class="error-code">403</div>
        <h1>Access Forbidden</h1>
        <p>Sorry, you don't have permission to access this page.<br>If you believe this is a mistake, please contact the website administrator.</p>
        <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn">Back to Homepage →</a>
        <div class="footer">Error 403 · Access Denied</div>
    </main>
</body>
</html>
