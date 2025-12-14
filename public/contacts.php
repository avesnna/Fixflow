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

<main class="contacts-page">

  <section class="hero fade-in">
    <div class="container">
      <i class="fa-solid fa-envelope hero-icon"></i>
      <h1>Свяжитесь с нами</h1>
      <p class="hero-sub">
        Мы всегда открыты к вашим вопросам, предложениям и сотрудничеству.  
        Напишите нам — и мы ответим в ближайшее время.
      </p>
    </div>
  </section>

  
  <section class="about fade-in">
    <div class="container">
      <div class="info-grid">
        <div class="card">
          <i class="fa-solid fa-location-dot"></i>
          <h3>Наш адрес</h3>
          <p>Республика Беларусь, г. Минск, ул. Независимости, 4</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-phone"></i>
          <h3>Телефоны</h3>
          <p>+375 (29) 698-98-23<br>+375 (44) 586-23-56</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-envelope-open-text"></i>
          <h3>Email</h3>
          <p>fixflow@mail.ru</p>
        </div>
      </div>
    </div>
  </section>

  <section class="map fade-in">
    <div class="container">
      <h2>Мы на карте</h2>
      <div class="map-frame">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2350.527777792791!2d27.55851217698844!3d53.90233477239886!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46dbcfd9d28a3fd9%3A0x5bca2c942efad1a5!2z0J3QtdCy0YHQutC40Lkg0L_RgC4sIDQsINCc0L7RgdC60LLQsCwg0JHQvtC70YzRiNC-0LksINC60YDQsNC50YHQuNGC0LXQu9GM0YHRjNC60LAgMjIwMDEx!5e0!3m2!1sru!2sby!4v1700000000000!5m2!1sru!2sby" 
          width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </section>

</main>

<?php include '../app/views/partials/footer.php'; ?>

<script src="../../../public/js/script.js"></script>
</body>
</html>