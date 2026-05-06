/* ── CURSOR ── */
const cursor = document.querySelector('.cursor');
if (cursor) {
  document.addEventListener('mousemove', e => {
    cursor.style.left = e.clientX + 'px';
    cursor.style.top = e.clientY + 'px';
  });
  document.querySelectorAll('a, button, .service-row, .skill-col').forEach(el => {
    el.addEventListener('mouseenter', () => cursor.classList.add('expand'));
    el.addEventListener('mouseleave', () => cursor.classList.remove('expand'));
  });
}

/* ── NAV SCROLL ── */
const navbar = document.getElementById('navbar');
const backTop = document.getElementById('backTop');

window.addEventListener('scroll', () => {
  const y = window.scrollY;
  navbar.classList.toggle('scrolled', y > 60);
  backTop.classList.toggle('show', y > 500);
});

/* ── MOBILE MENU ── */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});

function closeMobile() {
  hamburger.classList.remove('open');
  mobileMenu.classList.remove('open');
}

/* ── SCROLL REVEAL ── */
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => {
  revealObserver.observe(el);
});

/* ── SKILL BARS ── */
const skillObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      document.querySelectorAll('.skill-bar-fill').forEach(bar => {
        const pct = bar.dataset.height;
        setTimeout(() => { bar.style.height = pct + '%'; }, 200);
      });
      skillObserver.disconnect();
    }
  });
}, { threshold: 0.3 });

const skillsSection = document.getElementById('skills');
if (skillsSection) skillObserver.observe(skillsSection);

/* ── CONTACT FORM ── */
const form = document.getElementById('contactForm');
if (form) {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('.form-submit');
    const msg = document.getElementById('formMsg');

    btn.textContent = 'Sending…';
    btn.disabled = true;

    // ── Swap this timeout block for Formspree fetch when ready ──
    // fetch('https://formspree.io/f/YOUR_ID', {
    //   method: 'POST',
    //   body: new FormData(this),
    //   headers: { 'Accept': 'application/json' }
    // }).then(r => {
    //   if (r.ok) { showSuccess(); } else { showError(); }
    // }).catch(() => showError());

    setTimeout(() => {
      btn.textContent = 'Send message →';
      btn.disabled = false;
      msg.textContent = 'Message received. I\'ll be in touch shortly.';
      form.reset();
      setTimeout(() => { msg.textContent = ''; }, 5000);
    }, 1400);

    function showSuccess() {
      btn.textContent = 'Send message →';
      btn.disabled = false;
      msg.textContent = 'Message received. I\'ll be in touch shortly.';
      form.reset();
    }
    function showError() {
      btn.textContent = 'Send message →';
      btn.disabled = false;
      msg.textContent = 'Something went wrong. Please email directly.';
    }
  });
}

/* ── STAGGER DELAYS ── */
document.querySelectorAll('.service-row').forEach((row, i) => {
  row.style.transitionDelay = (i * 0.06) + 's';
});
document.querySelectorAll('.exp-company-block').forEach((block, i) => {
  block.querySelectorAll('.reveal').forEach((el, j) => {
    el.style.transitionDelay = (i * 0.08 + j * 0.04) + 's';
  });
});