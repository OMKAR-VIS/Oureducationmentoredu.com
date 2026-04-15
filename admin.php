<?php
require_once __DIR__ . '/php/config.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Our Education Mentor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-crown me-2"></i>Admin Panel</h4>
            <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
            <li><a href="#students"><i class="fas fa-users me-2"></i>Students</a></li>
            <li><a href="#content"><i class="fas fa-upload me-2"></i>Upload Content</a></li>
            <li><a href="#tests"><i class="fas fa-clipboard-list me-2"></i>Manage Tests</a></li>
            <li><a href="#analytics"><i class="fas fa-chart-bar me-2"></i>Analytics</a></li>
            <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div id="dashboard" class="content-section active">
            <div class="page-header">
                <h2><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h2>
            </div>
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6"><div class="stat-card primary"><div class="stat-icon"><i class="fas fa-users"></i></div><div><h3 id="totalStudents">0</h3><p>Total Students</p></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card success"><div class="stat-icon"><i class="fas fa-video"></i></div><div><h3 id="totalVideos">0</h3><p>Total Videos</p></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card warning"><div class="stat-icon"><i class="fas fa-file-pdf"></i></div><div><h3 id="totalPdfs">0</h3><p>Total PDFs</p></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card info"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div><h3 id="avgProgress">0%</h3><p>Avg Progress</p></div></div></div>
            </div>
            <div class="row">
                <div class="col-lg-8"><div class="card"><div class="card-header"><h5><i class="fas fa-clock me-2"></i>Recent Activity</h5></div><div class="card-body" id="recentActivity"></div></div></div>
                <div class="col-lg-4"><div class="card"><div class="card-header"><h5><i class="fas fa-chart-pie me-2"></i>Course Stats</h5></div><canvas id="courseChart" height="200"></canvas></div></div>
            </div>
        </div>

        <div id="students" class="content-section">
            <div class="page-header"><h2><i class="fas fa-users me-2 text-success"></i>Students Management</h2></div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Students List</h5>
                    <input type="text" id="searchStudents" class="form-control" style="width: 250px;" placeholder="Search students...">
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Progress</th><th>Videos Watched</th><th>Joined</th><th>Actions</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="content" class="content-section">
            <div class="page-header"><h2><i class="fas fa-upload me-2 text-warning"></i>Upload Content</h2></div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><h5><i class="fas fa-video me-2"></i>Add YouTube Video</h5></div>
                        <div class="card-body">
                            <form id="videoForm">
                                <div class="mb-3"><label class="form-label">Course</label><input type="text" class="form-control" name="course" required></div>
                                <div class="mb-3"><label class="form-label">Video Title</label><input type="text" class="form-control" name="title" required></div>
                                <div class="mb-3"><label class="form-label">YouTube URL</label><input type="url" class="form-control" name="youtube_url" required></div>
                                <button type="submit" class="btn btn-primary w-100">Upload Video</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-success text-white"><h5><i class="fas fa-file-pdf me-2"></i>Upload PDF</h5></div>
                        <div class="card-body">
                            <form id="pdfForm" enctype="multipart/form-data">
                                <div class="mb-3"><label class="form-label">Course</label><input type="text" class="form-control" name="course" required></div>
                                <div class="mb-3"><label class="form-label">PDF Title</label><input type="text" class="form-control" name="title" required></div>
                                <div class="mb-3"><label class="form-label">Select PDF</label><input type="file" class="form-control" name="pdf_file" accept=".pdf" required></div>
                                <button type="submit" class="btn btn-success w-100">Upload PDF</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tests" class="content-section"><div class="alert alert-info">Test management is ready for next phase integration.</div></div>
        <div id="analytics" class="content-section"><div class="alert alert-info">Analytics module ready with live summary cards.</div></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/admin.js"></script>
</body>
</html>
