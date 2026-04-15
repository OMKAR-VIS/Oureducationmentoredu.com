<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "invalid";
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';

if ($email === '' || $password === '') {
    echo "invalid";
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    echo "invalid";
    exit();
}

if ($role === 'admin' && $user['role'] !== 'admin') {
    echo "invalid";
    exit();
}

if ($role === 'admin' && strtolower($user['email']) !== 'admin@oureducationmentor.com') {
    echo "invalid";
    exit();
}

if ($role === 'student' && $user['role'] !== 'student') {
    echo "invalid";
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

echo $user['role'] === 'admin' ? "admin" : "student";
