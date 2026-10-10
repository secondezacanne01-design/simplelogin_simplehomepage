<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = (string) ($_SESSION['username'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="home-page">
    <header class="top-bar">
        <a class="home-title" href="home.php">My Home</a>
        <form method="post" action="logout.php">
            <button class="button-small" type="submit">Logout</button>
        </form>
    </header>

    <main class="home">
        <section class="welcome-card">
            <span class="home-label">HOME</span>
            <h1>Welcome, <?= escape($username) ?>!</h1>
            <p>You are logged in. You can now continue to your account.</p>
        </section>
    </main>
</body>
</html>
