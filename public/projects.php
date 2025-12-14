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

$stmt = $db->prepare("SELECT * FROM projects WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Мои проекты — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-folder-open"></i></div>
      <h1>Мои проекты</h1>
      <p class="hero-sub">Все ваши активные и завершённые проекты в одном месте</p>
    </div>
</section>

<section class="about fade-in ">
 <nav class="breadcrumbs fade-in">
    <div class="container">
        <a href="/public/profile.php">Мой профиль</a>
        <span> / </span>
        <span class="current">Мои проекты</span>
    </div>
 </nav>
 <div class="container">

 <?php if (count($projects) === 0): ?>

    
    <div class="container" style="margin-bottom: 40px;">
        <i class="fa-regular fa-folder"></i>
        <h3>У вас пока нет проектов</h3>
        <p>Создайте первый проект, чтобы начать работу</p>
    </div>

 <?php else: ?>

    <div class="projects-list">

 <?php foreach ($projects as $p): ?>

    <?php
        $icon = [
            'квартира' => 'fa-building',
            'частный дом' => 'fa-house-chimney',
            'другое' => 'fa-layer-group'
        ][$p['type']];
    ?>

    <div class="project-card">
        
       
        <div class="project-icon">
            <i class="fa-solid <?= $icon ?>"></i>
        </div>

        
        <div class="project-content">

            
            <div class="project-header-row">
                <h3><?= htmlspecialchars($p['title']) ?></h3>
                <span class="project-type"><?= $p['type'] ?></span>
            </div>

           
            <div class="project-details-row">
                <p><strong>Комнат:</strong> <?= $p['num_rooms'] ?></p>
                <p><strong>Этажей:</strong> <?= $p['num_floors'] ?></p>

                <?php if ($p['budget_mode']): ?>
                    <p><strong>Бюджет:</strong> <?= $p['budget_spent'] ?> / <?= $p['budget_total'] ?> BYN</p>
                <?php else: ?>
                    <p><strong>Бюджет:</strong> не указан</p>
                <?php endif; ?>
            </div>

           
            <div class="project-buttons-row">
                <a href="/public/project.php?id=<?= $p['id'] ?>" class="project-btn blue">
                    <i class="fa-solid fa-arrow-right"></i> Открыть проект
                </a>
                <?php

 if (isset($p['status'])) {
    $is_completed = ($p['status'] === 'completed');
 } else {
    // fallback для старых проектов без budget_mode
    $is_completed = (
        $p['budget_mode'] &&
        $p['budget_total'] > 0 &&
        $p['budget_spent'] >= $p['budget_total']
    );
 }
 ?>


 <form action="/public/php/toggle_project_status.php" method="POST" class="project-status-form">
    <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
    
    <?php if ($is_completed): ?>
        <button type="submit" name="action" value="resume" class="project-btn green">
            <i class="fa-solid fa-rotate-left"></i> Возобновить
        </button>
    <?php else: ?>
        <button type="submit" name="action" value="finish" class="project-btn red ">
            <i class="fa-solid fa-check"></i> Завершить
        </button>
    <?php endif; ?>
 </form>

            </div>
            
        </div>
    
    <div class="project-card-actions">
        <a href="/public/edit-project.php?id=<?= $p['id'] ?>" class="action-btn edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="/public/php/delete_project.php" method="POST" onsubmit="return confirm('Удалить проект?');">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" class="action-btn delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
    </div>

 <?php endforeach; ?>

 </div>


 <?php endif; ?>

 <div class="profile-actions">
    <a href="/public/add-project.php" class="profile-btn btn-login">
    <i class="fa-solid fa-plus"></i> Добавить проект
    </a>
 </div>
 </div>
</section>

<?php include('../app/views/partials/footer.php'); ?>

<script src="./js/script.js"></script>

</body>
</html>

