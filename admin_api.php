<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(["ok" => false, "message" => "Unauthorized"]);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'summary';

if ($action === 'summary') {
    $students = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='student'")->fetch_assoc()['c'] ?? 0;
    $videos = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE content_type='video'")->fetch_assoc()['c'] ?? 0;
    $pdfs = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE content_type='pdf'")->fetch_assoc()['c'] ?? 0;
    $avgProgress = $conn->query("SELECT IFNULL(ROUND(AVG(progress)),0) AS p FROM student_profiles")->fetch_assoc()['p'] ?? 0;
    echo json_encode([
        "ok" => true,
        "data" => [
            "totalStudents" => (int)$students,
            "totalVideos" => (int)$videos,
            "totalPdfs" => (int)$pdfs,
            "avgProgress" => (int)$avgProgress
        ]
    ]);
    exit();
}

if ($action === 'students') {
    $sql = "SELECT u.id, u.name, u.email, u.mobile, u.created_at, IFNULL(sp.progress,0) AS progress, IFNULL(sp.videos_watched,0) AS videos_watched
            FROM users u
            LEFT JOIN student_profiles sp ON sp.user_id = u.id
            WHERE u.role='student'
            ORDER BY u.id DESC";
    $res = $conn->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode(["ok" => true, "data" => $rows]);
    exit();
}

if ($action === 'upload_video') {
    $course = trim($_POST['course'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $youtubeUrl = trim($_POST['youtube_url'] ?? '');
    if ($course === '' || $title === '' || $youtubeUrl === '') {
        echo json_encode(["ok" => false, "message" => "All fields required"]);
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO contents (content_type, course, title, youtube_url) VALUES ('video', ?, ?, ?)");
    $stmt->bind_param("sss", $course, $title, $youtubeUrl);
    $stmt->execute();
    echo json_encode(["ok" => true, "message" => "Video uploaded"]);
    exit();
}

if ($action === 'upload_pdf') {
    $course = trim($_POST['course'] ?? '');
    $title = trim($_POST['title'] ?? '');
    if ($course === '' || $title === '' || !isset($_FILES['pdf_file'])) {
        echo json_encode(["ok" => false, "message" => "All fields required"]);
        exit();
    }

    $uploadDir = dirname(__DIR__) . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmp = $_FILES['pdf_file']['tmp_name'];
    $name = basename($_FILES['pdf_file']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        echo json_encode(["ok" => false, "message" => "Only PDF allowed"]);
        exit();
    }

    $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    $dest = $uploadDir . '/' . $safeName;
    if (!move_uploaded_file($tmp, $dest)) {
        echo json_encode(["ok" => false, "message" => "Upload failed"]);
        exit();
    }

    $relativePath = 'uploads/' . $safeName;
    $stmt = $conn->prepare("INSERT INTO contents (content_type, course, title, file_path) VALUES ('pdf', ?, ?, ?)");
    $stmt->bind_param("sss", $course, $title, $relativePath);
    $stmt->execute();
    echo json_encode(["ok" => true, "message" => "PDF uploaded"]);
    exit();
}

if ($action === 'contents') {
    $res = $conn->query("SELECT id, content_type, course, title, youtube_url, file_path, created_at FROM contents ORDER BY id DESC");
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode(["ok" => true, "data" => $rows]);
    exit();
}

echo json_encode(["ok" => false, "message" => "Invalid action"]);
