async function fetchJson(url) {
    const res = await fetch(url);
    return res.json();
}

function logout() {
    fetch('php/logout.php').then(() => {
        window.location.href = 'index.html';
    });
}

async function loadCourses() {
    const result = await fetchJson('php/dashboard_api.php?action=courses');
    if (!result.ok) return;

    const grid = document.getElementById('courseGrid');
    const courses = Object.keys(result.data);
    if (!courses.length) {
        grid.innerHTML = '<p>No content uploaded yet.</p>';
        return;
    }

    grid.innerHTML = courses.map(course => `
        <div class="course-card" data-course="${course}">
            <h5 class="mb-1 text-capitalize">${course}</h5>
            <small>${result.data[course].length} items</small>
        </div>
    `).join('');

    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('click', () => renderCourse(result.data[card.dataset.course], card.dataset.course));
    });
}

function renderCourse(items, courseName) {
    const videoSection = document.getElementById('videoSection');
    const list = document.getElementById('videoList');
    const frame = document.getElementById('videoFrame');
    videoSection.style.display = 'block';

    const videos = items.filter(i => i.content_type === 'video');
    const pdfs = items.filter(i => i.content_type === 'pdf');

    list.innerHTML = `<h6 class="mb-2 text-capitalize">${courseName}</h6>` +
        videos.map((v, idx) => `
            <div class="video-item ${idx === 0 ? 'active' : ''}" data-url="${v.youtube_url || ''}">
                <i class="fas fa-play-circle me-2"></i>${v.title}
            </div>
        `).join('') +
        pdfs.map(p => `
            <a class="video-item d-block text-decoration-none" target="_blank" href="${p.file_path}">
                <i class="fas fa-file-pdf me-2 text-danger"></i>${p.title}
            </a>
        `).join('');

    if (videos.length) frame.src = videos[0].youtube_url;

    list.querySelectorAll('.video-item[data-url]').forEach(item => {
        item.addEventListener('click', () => {
            list.querySelectorAll('.video-item').forEach(x => x.classList.remove('active'));
            item.classList.add('active');
            frame.src = item.dataset.url;
        });
    });
}

async function loadProgress() {
    const res = await fetchJson('php/dashboard_api.php?action=progress');
    if (!res.ok) return;
    const progressWrap = document.getElementById('progressChart');
    progressWrap.innerHTML = `
        <div class="card p-3">
            <h6>Overall Progress</h6>
            <div class="progress mb-2">
                <div class="progress-bar" style="width:${res.data.progress}%">${res.data.progress}%</div>
            </div>
            <p class="mb-0">Videos Watched: ${res.data.videos_watched}</p>
        </div>
    `;
}

document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.sidebar-nav a').forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        const target = this.getAttribute('href').replace('#', '');
        document.getElementById('courses').style.display = target === 'courses' ? 'block' : 'none';
        document.getElementById('progress').style.display = target === 'progress' ? 'block' : 'none';
    });
});

window.logout = logout;
loadCourses();
loadProgress();
