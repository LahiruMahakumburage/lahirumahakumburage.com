/* ── CURSOR ── */
const cd = document.getElementById('cd');
const cr = document.getElementById('cr');
let mx=0,my=0,rx=0,ry=0;
if(cd && cr){
  document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY});
  (function raf(){
    rx+=(mx-rx)*.13; ry+=(my-ry)*.13;
    cd.style.left=mx+'px'; cd.style.top=my+'px';
    cr.style.left=rx+'px'; cr.style.top=ry+'px';
    requestAnimationFrame(raf);
  })();
  document.querySelectorAll('a,button,.spec-card,.sk-card,.dc,.ci,.proc-item,.exp-head,.role').forEach(el=>{
    el.addEventListener('mouseenter',()=>cr.classList.add('big'));
    el.addEventListener('mouseleave',()=>cr.classList.remove('big'));
  });
}

/* ── NAV SCROLL ── */
const nav = document.querySelector('nav');
const btt = document.querySelector('.btt');
window.addEventListener('scroll',()=>{
  const y = window.scrollY;
  btt?.classList.toggle('show', y > 500);
  const sections = document.querySelectorAll('section[id],.section[id]');
  let cur='';
  sections.forEach(s=>{ if(y >= s.offsetTop - 160) cur = s.id; });
  document.querySelectorAll('.nav-links a').forEach(a=>{
    a.classList.toggle('act', a.getAttribute('href')==='#'+cur);
  });
},{passive:true});

/* ── BURGER ── */
const burger = document.getElementById('burger');
const mobNav = document.getElementById('mob-nav');
burger?.addEventListener('click',()=>{
  burger.classList.toggle('on');
  mobNav?.classList.toggle('on');
});
function closeMob(){ burger?.classList.remove('on'); mobNav?.classList.remove('on'); }

/* ── SCROLL REVEAL ── */
const ro = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){ e.target.classList.add('in'); ro.unobserve(e.target); }
  });
},{threshold:.08, rootMargin:'0px 0px -40px 0px'});
document.querySelectorAll('.sr,.sr-l,.sr-r').forEach(el=>ro.observe(el));

/* stagger delays */
document.querySelectorAll('.spec-grid .spec-card').forEach((c,i)=>c.style.transitionDelay=(i*.08)+'s');
document.querySelectorAll('.sk-grid .sk-card').forEach((c,i)=>c.style.transitionDelay=(i*.06)+'s');
document.querySelectorAll('.proc-list .proc-item').forEach((c,i)=>c.style.transitionDelay=(i*.07)+'s');

/* ── SKILL BARS ── */
const skillObs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      document.querySelectorAll('.sk-fill').forEach((b,i)=>{
        setTimeout(()=>{ b.style.height = b.dataset.h+'%'; }, i*70+150);
      });
      skillObs.disconnect();
    }
  });
},{threshold:.2});
const skillSec = document.getElementById('skills');
if(skillSec) skillObs.observe(skillSec);

/* ── EXPERIENCE ACCORDION ── */
document.querySelectorAll('.exp-card').forEach((card,idx)=>{
  const head = card.querySelector('.exp-head');
  head?.addEventListener('click',()=>{
    const isOpen = card.classList.contains('open');
    document.querySelectorAll('.exp-card.open').forEach(c=>c.classList.remove('open'));
    if(!isOpen) card.classList.add('open');
  });
  if(idx === 0) card.classList.add('open');
});

/* ── CONTACT FORM — PHPMailer via send.php ── */
const form = document.getElementById('cf');
const cfMsg = document.getElementById('cf-msg');

form?.addEventListener('submit', async function(e) {
  e.preventDefault();

  const btn  = this.querySelector('.cf-send');
  const orig = btn.innerHTML;

  // ── Client-side validation ──
  const email = this.querySelector('#em').value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showMsg('Please enter a valid email address.', 'error');
    return;
  }

  // ── Loading state ──
  btn.innerHTML = `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"
      style="animation:spin .8s linear infinite">
      <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
    </svg>
    Sending…`;
  btn.disabled = true;
  clearMsg();

  try {
    const res = await fetch('send.php', {
      method: 'POST',
      body: new FormData(this)
    });

    const data = await res.json();

    if (res.ok && data.ok) {
      showMsg('✓ Message sent! I\'ll get back to you within 24 hours.', 'success');
      form.reset();
    } else {
      showMsg(data.msg || 'Something went wrong. Please try again.', 'error');
    }
  } catch (err) {
    showMsg('Connection error. Please email info@lahirumahakumburage.com directly.', 'error');
  } finally {
    btn.innerHTML = orig;
    btn.disabled  = false;
  }
});

function showMsg(text, type) {
  if (!cfMsg) return;
  cfMsg.textContent = text;
  cfMsg.style.color = type === 'success'
    ? '#4ade80'
    : '#f87171';
  if (type === 'success') {
    setTimeout(clearMsg, 7000);
  }
}

function clearMsg() {
  if (cfMsg) { cfMsg.textContent = ''; cfMsg.style.color = ''; }
}

/* spinner keyframe */
const style = document.createElement('style');
style.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
document.head.appendChild(style);