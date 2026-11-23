<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['answer' => 'Method not allowed.']);
    exit;
}

$question = $_POST['question'] ?? '';
if (!is_string($question) || mb_strlen($question, 'UTF-8') > 500) {
    http_response_code(400);
    echo json_encode(['answer' => 'Please enter a question of up to 500 characters.']);
    exit;
}

$question = mb_strtolower(trim($question), 'UTF-8');
if ($question === '') {
    http_response_code(400);
    echo json_encode(['answer' => 'Please enter a question.']);
    exit;
}

$topics = [
    ['keywords' => ['register', 'sign up', 'signup', 'create account', 'registration'],
     'answer' => 'To create an account, open the registration page and follow the instructions, if registration is enabled.'],
    ['keywords' => ['login', 'log in', 'sign in', 'signin', 'connect'],
     'answer' => 'Open the Login page and enter your account credentials.'],
    ['keywords' => ['logout', 'log out', 'sign out', 'signout'],
     'answer' => 'When signed in, use the Logout option to end your session.'],
    ['keywords' => ['profile', 'account details', 'my account'],
     'answer' => 'Visit the Profile page to view your account information.'],
    ['keywords' => ['note', 'notes'],
     'answer' => 'Look for the Notes section to view or manage notes, if this feature is available in your version of Obscura.'],
    ['keywords' => ['password', 'forgot password', 'reset'],
     'answer' => 'Check the Login page for a password recovery option. If none is available, contact the project team.'],
    ['keywords' => ['help', 'features', 'what can', 'how to use'],
     'answer' => 'I can help with login, registration, logout, profiles, notes and password-related questions.']
];

$answer = 'I can only answer questions about Obscura features. Try asking about login, registration, profile, notes or logout.';
foreach ($topics as $topic) {
    foreach ($topic['keywords'] as $keyword) {
        if (mb_strpos($question, $keyword, 0, 'UTF-8') !== false) {
            $answer = $topic['answer'];
            break 2;
        }
    }
}

echo json_encode(['answer' => $answer], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
