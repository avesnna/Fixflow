<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT COUNT(*) FROM projects 
                      WHERE user_id = :id 
                      AND status = 'active'");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$active_projects = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM projects 
                      WHERE user_id = :id 
                      AND status = 'completed'");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$completed_projects = $stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Профиль — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-house-user"></i></div>
      <h1>Мой профиль</h1>
      <p class="hero-sub">  
        Здесь будет храниться информация о вас и о ваших проектах 
      </p>
    </div>
</section>

<section class="about fade-in">
 <div class="container">

  <div class="profile-card">
  <div class="profile-avatar">
    <?= strtoupper(mb_substr($user['name'], 0, 1)) ?>
  </div>

  <div class="profile-info">
    <h3><?= htmlspecialchars($user['name']) ?></h3>
    <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
  </div>
  </div>

  <div class="profile-stats">
  <div class="profile-stat-card">
    <h4>Активных проектов</h4>
    <span><?= $active_projects ?></span>
  </div>

  <div class="profile-stat-card">
    <h4>Завершённых проектов</h4>
    <span><?= $completed_projects ?></span>
  </div>
  </div>

  <div class="profile-actions">
  <a href="user-analytics.php" class="profile-btn btn-login">
    <i class="fa-solid fa-chart-line"></i> Аналитика пользователя
  </a>

  <a href="/public/projects.php" class="profile-btn btn-login">
    <i class="fa-solid fa-folder-open"></i> Мои проекты
  </a>
  </div>
  </div>
</section>

<?php include('../app/views/partials/footer.php'); ?>

<script src="./js/script.js"></script>

</body>
</html>
