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


$stmt = $db->prepare("SELECT name FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $db->prepare("SELECT 
    COUNT(*) AS total,
    SUM(status = 'active') AS active,
    SUM(status = 'completed') AS completed
 FROM projects
 WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$proj = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT 
    SUM(budget_total) AS budget_total,
    SUM(budget_spent) AS budget_spent
 FROM projects 
 WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$fin = $stmt->fetch(PDO::FETCH_ASSOC);

$budget_total = $fin['budget_total'] ?? 0;
$budget_spent = $fin['budget_spent'] ?? 0;

$stmt = $db->prepare("
    SELECT 
        SUM(status='запланировано') AS planned,
        SUM(status='в процессе') AS inprogress,
        SUM(status='выполнено') AS done,
        COUNT(*) AS total
    FROM actions 
    WHERE project_id IN (SELECT id FROM projects WHERE user_id = :uid)
");
$stmt->execute([':uid' => $user_id]);
$actions = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT title, cost 
    FROM actions 
    WHERE project_id IN (SELECT id FROM projects WHERE user_id = :uid)
    ORDER BY cost DESC
    LIMIT 5
");
$stmt->execute([':uid' => $user_id]);
$top_expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Аналитика пользователя — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-chart-line"></i></div>
      <h1>Аналитика пользователя</h1>
      <p class="hero-sub">
        Сводка всей вашей активности, финансов и прогресса по проектам
      </p>
    </div>
</section>

<section class="about fade-in">
 <nav class="breadcrumbs fade-in">
    <div class="container">
        <a href="/public/profile.php">Мой профиль</a>
        <span>/</span>
        <span class="current">Аналитика пользователя</span>
    </div>
 </nav>

 <div class="container">

 <div class="profile-stats" style="margin-bottom:25px;">

  <div class="profile-stat-card">
    <h4>Всего проектов</h4>
    <span><?= $proj['total'] ?></span>
  </div>

  <div class="profile-stat-card">
    <h4>Активных</h4>
    <span><?= $proj['active'] ?></span>
  </div>

  <div class="profile-stat-card">
    <h4>Завершённых</h4>
    <span><?= $proj['completed'] ?></span>
  </div>

  <div class="profile-stat-card">
    <h4>Всего задач</h4>
    <span><?= $actions['total'] ?></span>
  </div>

 </div>

 <div class="project-card multi fade-in">
    <div class="info-header">
        <h2><i class="fa-solid fa-wallet"></i> Финансовая статистика</h2>
    </div>

    <?php if ($budget_total > 0): ?>
        <p><strong>Общий бюджет проектов:</strong> <?= $budget_total ?> BYN</p>
        <p><strong>Потрачено:</strong> <?= $budget_spent ?> BYN</p>

        <div class="progress-bar">
            <div class="progress" style="width: <?= min(100, $budget_spent / $budget_total * 100) ?>%"></div>
        </div>
    <?php else: ?>
        <p>Финансовый контроль ещё не включён в проектах.</p>
    <?php endif; ?>
 </div>

 <div class="project-card multi fade-in">
    <div class="info-header">
        <h2><i class="fa-solid fa-list-check"></i> Задачи по статусам</h2>
    </div>

    <div class="chart-box">
    <canvas id="tasksChart"></canvas>
    </div>


    <script>
        new Chart(document.getElementById('tasksChart'), {
            type: 'doughnut',
            data: {
                labels: ["Запланировано", "В процессе", "Выполнено"],
                datasets: [{
                    data: [<?= $actions['planned'] ?>, <?= $actions['inprogress'] ?>, <?= $actions['done'] ?>],
                    backgroundColor: ["#6bb4ff", "#ffd86b", "#6bff9b"]
                }]
            }
        });
    </script>
 </div>

 <div class="project-card multi fade-in">
    <div class="info-header">
        <h2><i class="fa-solid fa-coins"></i> Самые дорогие задачи</h2>
    </div>

    <?php if ($top_expenses): ?>
        <ul class="styled-list">
            <?php foreach ($top_expenses as $row): ?>
                <li><strong><?= htmlspecialchars($row['title']) ?></strong> — <?= $row['cost'] ?> BYN</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Пока нет добавленных затрат.</p>
    <?php endif; ?>
 </div>

 </div>
</section>

<?php include('../app/views/partials/footer.php'); ?>

<script src="./js/script.js"></script>

</body>
</html>
