
<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html?signup=1');
    exit;
}

$expectsJson = ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';

$lastName = trim(is_string($_POST['last_name'] ?? null) ? $_POST['last_name'] : '');
$firstName = trim(is_string($_POST['first_name'] ?? null) ? $_POST['first_name'] : '');
$username = trim(is_string($_POST['username'] ?? null) ? $_POST['username'] : '');
$password = $_POST['password'] ?? '';
$confirmation = $_POST['confirm_password'] ?? '';

if (!preg_match('/^.{1,100}$/usD', $lastName) || !preg_match('/^.{1,100}$/usD', $firstName)) {
    $error = 'Enter a last name and first name of up to 100 characters each.';
} elseif (!preg_match('/^[a-zA-Z0-9_.-]{1,50}$/D', $username)) {
    $error = 'Enter a username of 1 to 50 letters, numbers, dots, hyphens, or underscores.';
} elseif (!is_string($password) || strlen($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false) {
    $error = 'Use a password between 8 and 72 bytes.';
} elseif ($password !== $confirmation) {
    $error = 'The passwords do not match.';
} else {
    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (last_name, first_name, username, password) VALUES (?, ?, ?, ?)';

        if (extension_loaded('mysqli')) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $connection = new mysqli('localhost', 'root', '', 'login2_db');
            $connection->set_charset('utf8mb4');
            $statement = $connection->prepare($sql);
            $statement->bind_param('ssss', $lastName, $firstName, $username, $hash);
            $statement->execute();
        } else {
            $connection = new PDO('mysql:host=localhost;dbname=login2_db;charset=utf8mb4', 'root', '');
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $statement = $connection->prepare($sql);
            $statement->execute([$lastName, $firstName, $username, $hash]);
        }

        if ($expectsJson) {
            http_response_code(204);
            exit;
        }
        header('Location: login.html?registered=1', true, 303);
        exit;
    } catch (PDOException | mysqli_sql_exception $exception) {
        $code = $exception instanceof PDOException ? ($exception->errorInfo[1] ?? null) : $exception->getCode();
        $error = (int) $code === 1062
            ? 'That username is already taken.'
            : 'Account creation is unavailable right now. Please try again later.';
    }
}

if ($expectsJson) {
    http_response_code(422);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $error]);
    exit;
}

header('Location: login.html?signup=1&signupError=' . urlencode($error), true, 303);
exit;