<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>О сервисе — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="../../../public/img/favicon.svg">

  <link rel="stylesheet" href="../../../public/css/style.css" />
</head>
<body>

  <?php include '../app/views/partials/header.php'; ?>

  <section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-lightbulb"></i></div>
      <h1>О сервисе FixFlow</h1>
      <p class="hero-sub">
      Мы создаём цифровую платформу, которая делает ремонт прозрачным, удобным и предсказуемым
      </p>
    </div>
  </section>

  <section class="about fade-in">
    <div class="container">
      <h2>Наша миссия</h2>
      <p>
        FixFlow помогает людям, управляющим компаниям и подрядчикам вести ремонтные проекты
        без хаоса и задержек. Мы объединили планирование, аналитику и коммуникацию в одном месте 
      </p>

      <div class="about-grid">
        <div class="card">
          <i class="fa-solid fa-gears"></i>
          <h3>Автоматизация процессов</h3>
          <p>Система сама напоминает о сроках, формирует отчёты и контролирует выполнение задач</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-users"></i>
          <h3>Командная работа</h3>
          <p>Добавляйте коллег, мастеров и заказчиков — всё взаимодействие происходит в одном окне</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-chart-line"></i>
          <h3>Аналитика и эффективность</h3>
          <p>Финансовые отчёты и графики позволяют принимать решения на основе данных, а не догадок</p>
        </div>
      </div>
    </div>
  </section>

  <?php include '../app/views/partials/footer.php'; ?>

  <script src="../../../public/js/script.js"></script>
</body>
</html>
