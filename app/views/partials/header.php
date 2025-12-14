
<div id="toast" class="toast" aria-hidden="true">Вы не авторизованы!</div>
<header class="header">
  <div class="container header-inner">
    <div class="logo">
      <span>Fix</span>Flow
    </div>

    
    <button class="burger" id="burger" aria-label="Открыть меню">
      <i class="fa-solid fa-bars"></i>
    </button>

    
    <nav class="nav-desktop">
      <a href="../../../public/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Главная</a>
      <a href="../../../public/about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">О сервисе</a>
      <a href="../../../public/contacts.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : '' ?>">Контакты</a>
      <a href="<?php echo isset($_SESSION['user_id']) ? '../../../public/profile.php' : '#'; ?>"
       class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>"
       onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'showToast(\'Вы не  авторизованы!\');   return false;'; ?>">
       Мой профиль
      </a>
    </nav>

    
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="../../../public/php/logout.php" class="btn-login desktop-login">Выйти</a>
    <?php else: ?>
      <a href="#" class="btn-login desktop-login" id="openAuthModal">Войти</a>
    <?php endif; ?>
  </div>

 
  <nav class="nav" id="nav" aria-hidden="true">
    <div class="nav-inner">
      <div class="nav-header">
        <div class="logo">Fix<span>Flow</span></div>
        <button class="close-btn" id="closeNav" aria-label="Закрыть меню">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="nav-links">
        <a href="../../../public/index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Главная</a>
        <a href="../../../public/about.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">О сервисе</a>
        <a href="../../../public/contacts.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : '' ?>">Контакты</a>
        <a href="<?php echo isset($_SESSION['user_id']) ? '../../../public/profile.php' : '#'; ?>"
         class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>"
         onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'showToast(\'Вы не авторизованы!\'); return false;'; ?>">
         Мой профиль
        </a>
      </div>

      
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="../../../public/php/logout.php" class="btn-login mobile-login">Выйти</a>
      <?php else: ?>
        <a href="#" class="btn-login mobile-login" id="openAuthModalMobile">Войти</a>
      <?php endif; ?>
    </div>

    
    <div class="nav-overlay" id="navOverlay"></div>
  </nav>

  
  <div class="modal" id="authModal" aria-hidden="true">
    <div class="modal-content">
      <button class="close-modal" id="closeAuthModal">&times;</button>
      <div class="auth-tabs">
        <button class="tab-btn active" data-tab="login">Вход</button>
        <button class="tab-btn" data-tab="register">Регистрация</button>
      </div>

      <div class="tab-content" id="login">
        <form id="loginForm" method="POST" action="../../../public/php/login.php">
          <input type="email" name="email" placeholder="Email" required>
          <div class="password-wrapper">
          <input type="password" name="password" placeholder="Пароль" required>
          <i class="fa-solid fa-eye toggle-password"></i>
          </div>
          <button type="submit">Войти</button>
        </form>
      </div>

      <div class="tab-content" id="register" style="display:none;">
        <form id="registerForm" method="POST" action="../../../public/php/register.php">
          <input type="text" name="name" placeholder="Имя" required>
          <input type="email" name="email" placeholder="Email" required>
          <div class="password-wrapper">
          <input type="password" name="password" placeholder="Пароль" required>
          <i class="fa-solid fa-eye toggle-password"></i>
          </div>
          
          <button type="submit">Зарегистрироваться</button>
        </form>
      </div>
    </div>
    <div class="modal-overlay" id="modalOverlay"></div>
  </div>

</header>

