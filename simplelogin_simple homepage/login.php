<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!valid_csrf_token()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Enter your username and password.';
    } else {
        $statement = database()->prepare(
            'SELECT id, username, password FROM users WHERE username = :username LIMIT 1'
        );
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: home.php');
            exit;
        }

        $error = 'Incorrect username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="page">
        <section class="form-card">
            <h1>Login</h1>
            <p class="intro">Sign in to continue.</p>

            <?php if ($error !== ''): ?>
                <p class="message error" role="alert"><?= escape($error) ?></p>
            <?php endif; ?>

            <form method="post" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">

                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       value="<?= escape($username) ?>" maxlength="50" required autofocus>

                <label for="password">Password</label>
                <input id="password" name="password" type="password"
                       autocomplete="current-password" required>

                <button type="submit">Login</button>
            </form>

            <p class="switch-link">Don’t have an account? <a href="register.php">Create one</a></p>
        </section>
    </main>
</body>
</html>
