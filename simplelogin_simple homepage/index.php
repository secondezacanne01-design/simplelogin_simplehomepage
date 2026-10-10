<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Location: ' . (isset($_SESSION['user_id']) ? 'home.php' : 'login.php'));
exit;
