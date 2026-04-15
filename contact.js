const contactForm = document.getElementById('contactForm');
contactForm?.addEventListener('submit', function(e) {
  e.preventDefault();
  const btn = this.querySelector('.submit-btn');
  btn.disabled = true;
  btn.textContent = 'Sending...';
  setTimeout(() => {
    showToast('Message sent successfully!', 'success');
    this.reset();
    btn.disabled = false;
    btn.textContent = 'Send Message';
  }, 1000);
});

document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', () => {
    const ans = q.nextElementSibling;
    const open = ans.classList.contains('active');
    document.querySelectorAll('.faq-answer').forEach(x => x.classList.remove('active'));
    if (!open) ans.classList.add('active');
  });
});

function showToast(msg, type) {
  let box = document.getElementById('toastBox');
  if (!box) {
    box = document.createElement('div');
    box.id = 'toastBox';
    box.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
    document.body.appendChild(box);
  }
  box.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  setTimeout(() => { box.innerHTML = ''; }, 3000);
}
