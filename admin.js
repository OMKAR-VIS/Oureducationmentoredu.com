function showSection(hash) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
    const id = hash.replace('#', '') || 'dashboard';
    const section = document.getElementById(id);
    if (section) section.classList.add('active');
    const link = document.querySelector(`.sidebar-menu a[href="#${id}"]`);
    if (link) link.classList.add('active');
}

document.querySelectorAll('.sidebar-menu a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        showSection(this.getAttribute('href'));
    });
});

async function fetchJson(url, options) {
    const res = await fetch(url, options);
    return res.json();
}

async function loadSummary() {
    const res = await fetchJson('php/admin_api.php?action=summary');
    if (!res.ok) return;
    document.getElementById('totalStudents').textContent = res.data.totalStudents;
    document.getElementById('totalVideos').textContent = res.data.totalVideos;
    document.getElementById('totalPdfs').textContent = res.data.totalPdfs;
    document.getElementById('avgProgress').textContent = `${res.data.avgProgress}%`;

    const activity = document.getElementById('recentActivity');
    activity.innerHTML = `
        <p class="mb-2">New students: ${res.data.totalStudents}</p>
        <p class="mb-2">Videos uploaded: ${res.data.totalVideos}</p>
        <p class="mb-0">PDFs uploaded: ${res.data.totalPdfs}</p>
    `;

    const ctx = document.getElementById('courseChart');
    if (ctx && window.Chart) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Videos', 'PDFs'],
                datasets: [{
                    data: [res.data.totalVideos, res.data.totalPdfs],
                    backgroundColor: ['#2563eb', '#16a34a']
                }]
            }
        });
    }
}

async function loadStudents() {
    const res = await fetchJson('php/admin_api.php?action=students');
    if (!res.ok) return;
    const tbody = document.querySelector('#studentsTable tbody');
    tbody.innerHTML = res.data.map(st => `
        <tr>
            <td>${st.id}</td>
            <td>${st.name}</td>
            <td>${st.email}</td>
            <td>${st.mobile || '-'}</td>
            <td>${st.progress}%</td>
            <td>${st.videos_watched}</td>
            <td>${new Date(st.created_at).toLocaleDateString()}</td>
            <td><span class="badge bg-success">Active</span></td>
        </tr>
    `).join('');
}

document.getElementById('videoForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'upload_video');
    const res = await fetchJson('php/admin_api.php', { method: 'POST', body: formData });
    alert(res.message || 'Done');
    if (res.ok) {
        this.reset();
        loadSummary();
    }
});

document.getElementById('pdfForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'upload_pdf');
    const res = await fetchJson('php/admin_api.php', { method: 'POST', body: formData });
    alert(res.message || 'Done');
    if (res.ok) {
        this.reset();
        loadSummary();
    }
});

document.getElementById('searchStudents')?.addEventListener('input', function() {
    const value = this.value.toLowerCase();
    document.querySelectorAll('#studentsTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

function logout() {
    fetch('php/logout.php').then(() => {
        window.location.href = 'index.html';
    });
}

loadSummary();
loadStudents();
window.logout = logout;
