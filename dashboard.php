<?php
require_once __DIR__ . '/php/config.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'student') {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Our Education Mentor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="image/OUR_EDUCATION_MENTOR'S_png.png" alt="logo" class="logo-img">
            <span class="ms-2 fw-bold">OUR EDUCATION MENTOR</span>
        </a>
        <div class="ms-auto d-flex gap-3 align-items-center">
            <span class="welcome-text">Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
            <a class="nav-link text-white" href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <div class="sidebar sticky-top">
                <div class="user-profile">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <h6 class="mb-0"><?= htmlspecialchars($_SESSION['user_name']) ?></h6>
                        <small>ID: <?= (int)$_SESSION['user_id'] ?></small>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    <a href="#courses" class="active"><i class="fas fa-play-circle me-2"></i>Videos</a>
                    <a href="#progress"><i class="fas fa-chart-line me-2"></i>Progress</a>
                </nav>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="welcome-card">
                <h3><i class="fas fa-graduation-cap text-primary"></i> Welcome Back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
                <p>Continue your learning journey with new videos and notes.</p>
            </div>

            <div id="courses">
                <h4 class="section-title"><i class="fas fa-book-open me-2"></i>Select Course</h4>
                <div class="course-grid" id="courseGrid"></div>
            </div>

            <div id="videoSection" class="video-section" style="display:none;">
                <div class="row">
                    <div class="col-lg-8"><div class="video-player"><iframe id="videoFrame" src="" allowfullscreen></iframe></div></div>
                    <div class="col-lg-4"><div class="video-list" id="videoList"></div></div>
                </div>
            </div>

            <div id="progress" class="mt-5" style="display:none;">
                <h4 class="section-title"><i class="fas fa-chart-bar me-2"></i>Your Progress</h4>
                <div id="progressChart"></div>
            </div>
        </div>
    </div>
</div>
<script src="js/dashboard.js"></script>
</body>
</html>
