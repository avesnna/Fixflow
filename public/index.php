<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FixFlow — система учета и планирования ремонтов</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

  <section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-water"></i></div>
      <h1>Ремонт без задержек и хлопот, как по течению</h1>
      <p class="hero-sub">
        <strong>FixFlow</strong> — это умная система, которая помогает планировать, контролировать и учитывать все ремонтные работы в вашем доме
      </p>
    </div>
  </section>
  
  <section class="how fade-in">
    <div class="container">
      <h2>Как это работает ?</h2>
      <div class="how-cards">
        <div class="card">
          <i class="fa-regular fa-clipboard"></i>
          <h3>Планируйте</h3>
          <p>Создайте список задач, установите приоритеты и сроки. Разбейте большой ремонт на простые шаги</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-hammer"></i>
          <h3>Исполняйте</h3>
          <p>Находите проверенных мастеров, отслеживайте прогресс по этапам и контролируйте бюджет</p>
        </div>
        <div class="card">
          <i class="fa-regular fa-circle-check"></i>
          <h3>Учитывайте</h3>
          <p>Вся история ремонтов и финансов у вас под рукой. Удобно для ТСЖ, УК или для себя</p>
        </div>
      </div>
    </div>
  </section>

  <section class="features fade-in">
    <div class="container">
      <div class="feature"><i class="fa-solid fa-chart-pie"></i><h3>Супер аналитика</h3><p>Отслеживайте бюджет, сроки и прогресс по каждому проекту в реальном времени. Статистика и отчеты — для тех, кто любит цифры!</p></div>
      <div class="feature"><i class="fa-solid fa-list-check"></i><h3>Множество проектов</h3><p>Ведите хоть 5 ремонтов одновременно — квартира, дом или объекты клиентов. Ничего не перепутается.</p></div>
      <div class="feature"><i class="fa-solid fa-box-open"></i><h3>Всё в одном месте</h3><p>Планирование, сметы, мастера, документы — работайте из любого устройства.</p></div>
    </div>
  </section>

  <section class="partners fade-in">
    <div class="container">
      <h2>Наши партнёры</h2>
      <div class="partners-grid">
        <div class="partner-card ikea" onclick="window.open('https:/aikea.by/', '_blank')">
          <div class="partner-image">
            <img src="./img/ikea.png" alt="IKEA">
          </div>
          <p><strong>Скидка 30% </strong> на покупку от 2000 BYN по промокоду <strong>FIXFLOW</strong></p>
        </div>
        <div class="partner-card philips" onclick="window.open('https:/www.philips.by/', '_blank')">
          <div class="partner-image">
            <img src="./img/philips.jpg" alt="Philips">
          </div>
          <p><strong>Скидка 10%</strong> на товары Philips SmartSleep по промокоду <strong>FIXFLOW</strong></p>
        </div>
        <div class="partner-card hoff" onclick="window.open('https:/hoff.ru/', '_blank')">
          <div class="partner-image">
            <img src="./img/hoff.jpg" alt="Hoff">
          </div>
          <p>Эксклюзивный выбор мебели по приятной цене только для пользователей <strong>FixFlow</strong></p>
        </div>
        <div class="partner-card sber" onclick="window.open('https:/www.sber-bank.by/', '_blank')">
          <div class="partner-image">
            <img src="./img/sber.png" alt="Sber">
          </div>
          <p>Карта «FixFlow & Sber»: <strong> рассрочка без процентов с выгодой до 10%</strong></p>
        </div>
      </div>
    </div>
  </section>

<?php include('../app/views/partials/footer.php'); ?>

  <script src="./js/script.js"></script>

  <?php if (!empty($_SESSION['toast_message'])): ?>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        showToast("<?php echo $_SESSION['toast_message']; ?>");
    });
  </script>

<?php unset($_SESSION['toast_message']); endif; ?>

</body>
</html>
