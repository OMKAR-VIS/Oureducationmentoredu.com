// ======= NAVBAR TOGGLE =======
const menuToggle = document.getElementById('menu-toggle');
const menu = document.getElementById('menu');

if (menuToggle && menu) {
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!menu.contains(e.target) && !menuToggle.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
}

// ======= LOGIN MODAL =======
const loginModal = document.getElementById('loginModal');

function openModal() {
    if (!loginModal) return;
    loginModal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModalFn() {
    if (!loginModal) return;
    loginModal.classList.remove('open');
    document.body.style.overflow = '';
}

const openLogin = document.getElementById('openLogin');
if (openLogin) {
    openLogin.addEventListener('click', function(e) {
        e.preventDefault();
        openModal();
    });
}

// Mobile login button
const openLoginMobile = document.getElementById('openLoginMobile');
if (openLoginMobile) {
    openLoginMobile.addEventListener('click', function(e) {
        e.preventDefault();
        if (menu) menu.classList.remove('active');
        openModal();
    });
}

const closeModal = document.getElementById('closeModal');
if (closeModal) {
    closeModal.addEventListener('click', closeModalFn);
}

if (loginModal) {
    loginModal.addEventListener('click', function(e) {
        if (e.target === loginModal) closeModalFn();
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModalFn();
});

// ======= LOGIN FORM =======
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('loginBtn');
        if (!btn) return;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('php/login_process.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.text())
        .then(resp => {
            resp = resp.trim();
            if (resp === 'admin') {
                showAlert('✅ Admin login successful. Redirecting to admin panel...', 'success');
                setTimeout(() => { window.location.href = 'admin.php'; }, 1500);
            } else if (resp === 'student') {
                showAlert('✅ Login successful. Redirecting to dashboard...', 'success');
                setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
            } else {
                showAlert('❌ Invalid email or password.', 'danger');
                btn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
                btn.disabled = false;
            }
        })
        .catch(() => {
            showAlert('❌ Server error. Please try again.', 'danger');
            btn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
            btn.disabled = false;
        });
    });
}

// ======= ALERT =======
function showAlert(msg, type) {
    const box = document.getElementById('alertBox');
    if (!box) return;
    box.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert" style="border-radius:12px; min-width:280px;">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    setTimeout(() => {
        box.innerHTML = '';
    }, 4000);
}

const registerModal = document.getElementById('registerModal');
const openRegisterIds = ['openRegister', 'openRegisterHero', 'openRegisterMobile', 'openRegisterFromLogin'];
const closeRegister = document.getElementById('closeRegisterModal');
const openLoginFromRegister = document.getElementById('openLoginFromRegister');
const registerModalForm = document.getElementById('registerModalForm');

function openRegisterModal() {
    if (!registerModal) return;
    closeModalFn();
    registerModal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeRegisterModal() {
    if (!registerModal) return;
    registerModal.classList.remove('open');
    document.body.style.overflow = '';
}

openRegisterIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            if (menu) menu.classList.remove('active');
            openRegisterModal();
        });
    }
});

if (closeRegister) {
    closeRegister.addEventListener('click', closeRegisterModal);
}

if (openLoginFromRegister) {
    openLoginFromRegister.addEventListener('click', function(e) {
        e.preventDefault();
        closeRegisterModal();
        openModal();
    });
}

if (registerModal) {
    registerModal.addEventListener('click', function(e) {
        if (e.target === registerModal) closeRegisterModal();
    });
}

if (registerModalForm) {
    registerModalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('registerModalBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        btn.disabled = true;

        fetch('php/register_process.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(r => r.json())
        .then(resp => {
            if (resp.ok) {
                showAlert('✅ Registration successful. Please login.', 'success');
                closeRegisterModal();
                this.reset();
            } else {
                showAlert(`❌ ${resp.message || 'Registration failed'}`, 'danger');
            }
            btn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Register';
            btn.disabled = false;
        })
        .catch(() => {
            showAlert('❌ Server error. Please try again.', 'danger');
            btn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Register';
            btn.disabled = false;
        });
    });
}
