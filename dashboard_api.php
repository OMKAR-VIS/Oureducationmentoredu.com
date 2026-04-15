<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(["ok" => false, "message" => "Unauthorized"]);
    exit();
}

$action = $_GET['action'] ?? 'courses';

if ($action === 'courses') {
    $res = $conn->query("SELECT id, content_type, course, title, youtube_url, file_path FROM contents ORDER BY id DESC");
    $byCourse = [];
    while ($row = $res->fetch_assoc()) {
        $course = strtolower($row['course']);
        if (!isset($byCourse[$course])) {
            $byCourse[$course] = [];
        }
        $byCourse[$course][] = $row;
    }
    echo json_encode(["ok" => true, "data" => $byCourse]);
    exit();
}

if ($action === 'progress') {
    $uid = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT progress, videos_watched FROM student_profiles WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        $row = ["progress" => 0, "videos_watched" => 0];
    }
    echo json_encode(["ok" => true, "data" => $row]);
    exit();
}

echo json_encode(["ok" => false, "message" => "Invalid action"]);
