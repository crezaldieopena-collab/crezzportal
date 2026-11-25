<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.html?error=' . urlencode('Please enter both username and password.'));
    exit;
}

function redirectWithError(string $message): void
{
    header('Location: login.html?error=' . urlencode($message));
    exit;
}

try {
    if (extension_loaded('mysqli')) {
        $conn = mysqli_connect('localhost', 'root', '', 'login2_db');
        if (!$conn) {
            throw new RuntimeException('Database connection failed. Please create the database first.');
        }

        $stmt = mysqli_prepare($conn, 'SELECT id, username, password, first_name, last_name FROM users WHERE username = ?');
        if (!$stmt) {
            throw new RuntimeException('Unable to prepare the database query.');
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        $pdo = new PDO('mysql:host=localhost;dbname=login2_db;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT id, username, password, first_name, last_name FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;
    }

    $storedPassword = $user['password'] ?? '';
    $validPassword = password_verify($password, $storedPassword)
        || (preg_match('/^[a-f0-9]{32}$/D', $storedPassword)
            && hash_equals($storedPassword, md5($password)));

    if ($user && $validPassword) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        header('Location: welcome.php');
        exit;
    }

    redirectWithError('Invalid username or password.');
} catch (Throwable $e) {
    redirectWithError($e->getMessage());
}