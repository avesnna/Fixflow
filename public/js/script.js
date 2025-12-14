// === Fade-in on scroll ===
const fadeElements = document.querySelectorAll('.fade-in');
const appearOnScroll = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    entry.target.classList.add('visible');
    observer.unobserve(entry.target);
  });
}, { threshold: 0.2 });

fadeElements.forEach(el => appearOnScroll.observe(el));

// === Scroll to top (footer icons) ===
document.querySelectorAll('.footer-icons i').forEach(icon => {
  icon.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});

// === Burger Menu ===
const burger = document.getElementById('burger');
const nav = document.getElementById('nav');
const closeNav = document.getElementById('closeNav');
const navOverlay = document.getElementById('navOverlay');

burger.addEventListener('click', () => {
  nav.classList.add('open');
});

closeNav.addEventListener('click', () => {
  nav.classList.remove('open');
});

navOverlay.addEventListener('click', () => {
  nav.classList.remove('open');
});

// Открытие модалки
const btnLoginDesktop = document.getElementById('openAuthModal');
const btnLoginMobile = document.getElementById('openAuthModalMobile');
const authModal = document.getElementById('authModal');
const modalOverlay = document.getElementById('modalOverlay');
const closeModalBtn = document.getElementById('closeAuthModal');

function openModal() {
  authModal.setAttribute('aria-hidden', 'false');
}

function closeModal() {
  authModal.setAttribute('aria-hidden', 'true');
}

btnLoginDesktop.addEventListener('click', openModal);
btnLoginMobile.addEventListener('click', openModal);
closeModalBtn.addEventListener('click', closeModal);
modalOverlay.addEventListener('click', closeModal);

// Переключение вкладок
const tabs = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    tabContents.forEach(tc => tc.style.display = 'none');
    document.getElementById(tab.dataset.tab).style.display = 'block';
  });
});

// Показ/скрытие пароля по клику на i
const togglePasswordIcons = document.querySelectorAll('.toggle-password');

togglePasswordIcons.forEach(icon => {
  icon.addEventListener('click', () => {
    const input = icon.previousElementSibling;
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });
});

function showToast(message) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.add('show');
  toast.setAttribute('aria-hidden', 'false');

  setTimeout(() => {
    toast.classList.remove('show');
    toast.setAttribute('aria-hidden', 'true');
  }, 3000); 
}

