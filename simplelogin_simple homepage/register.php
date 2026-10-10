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
    $confirmation = (string) ($_POST['confirm_password'] ?? '');

    if (!valid_csrf_token()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = 'Use 3–50 letters, numbers, or underscores for your username.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters.';
    } elseif ($password !== $confirmation) {
        $error = 'The passwords do not match.';
    } else {
        $statement = database()->prepare(
            'INSERT INTO users (username, password) VALUES (:username, :password)'
        );

        try {
            $statement->execute([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) database()->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: home.php');
            exit;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'That username is already taken.';
            } else {
                throw $exception;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="page">
        <section class="form-card">
            <h1>Create account</h1>
            <p class="intro">Choose a username and password.</p>

            <?php if ($error !== ''): ?>
                <p class="message error" role="alert"><?= escape($error) ?></p>
            <?php endif; ?>

            <form method="post" action="register.php">
                <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">

                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       value="<?= escape($username) ?>" minlength="3" maxlength="50" required autofocus>
                <small>3–50 letters, numbers, or underscores.</small>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password"
                       minlength="8" required>
                <small>At least 8 characters.</small>

                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" name="confirm_password" type="password"
                       autocomplete="new-password" minlength="8" required>

                <button type="submit">Create account</button>
            </form>

            <p class="switch-link">Already registered? <a href="login.php">Login</a></p>
        </section>
    </main>
</body>
</html>
