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

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$project_id) {
    header("Location: /public/projects.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM projects WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $project_id, ':uid' => $user_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: /public/projects.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $num_rooms = intval($_POST['num_rooms']);
    $num_floors = intval($_POST['num_floors']);
    $budget_mode = isset($_POST['budget_mode']) ? 1 : 0;
    $budget_total = $budget_mode ? floatval($_POST['budget_total']) : 0;

    if ($title === '') {
        $_SESSION['toast_message'] = "Название проекта не может быть пустым";
        header("Location: /public/edit-project.php?id=".$project_id);
        exit;
    }

    $stmt = $db->prepare("
        UPDATE projects SET 
            title = :title,
            type = :type,
            num_rooms = :num_rooms,
            num_floors = :num_floors,
            budget_mode = :budget_mode,
            budget_total = :budget_total
        WHERE id = :id AND user_id = :uid
    ");

    $stmt->execute([
        ':title' => $title,
        ':type' => $type,
        ':num_rooms' => $num_rooms,
        ':num_floors' => $num_floors,
        ':budget_mode' => $budget_mode,
        ':budget_total' => $budget_total,
        ':id' => $project_id,
        ':uid' => $user_id
    ]);

    $_SESSION['toast_message'] = "Изменения сохранены!";
    header("Location: /public/projects.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Редактировать проект — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-pen-to-square"></i></div>
      <h1>Редактировать проект</h1>
      <p class="hero-sub">Измените данные проекта</p>
    </div>
</section>

<section class="about fade-in">

 <nav class="breadcrumbs fade-in">
    <div class="container">
        <a href="/public/profile.php">Мой профиль</a>
        <span>/</span>
        <a href="/public/projects.php">Мои проекты</a>
        <span>/</span>
        <span class="current">Редактировать проект</span>
    </div>
 </nav>

 <div class="container">

 <div class="project-form-card">
    <h2>Редактировать проект</h2>

 <form method="POST" class="project-form">

 <label>Название проекта</label>
 <input type="text" name="title" required value="<?= htmlspecialchars($project['title']) ?>">

 <label>Тип объекта</label>
 <div class="project-type-select">

    <div class="type-option <?= ($project['type']=='квартира'?'active':'') ?>" data-type="квартира">
        <i class="fa-solid fa-building"></i> Квартира
    </div>

    <div class="type-option <?= ($project['type']=='частный дом'?'active':'') ?>" data-type="частный дом">
        <i class="fa-solid fa-house-chimney"></i> Частный дом
    </div>

    <div class="type-option <?= ($project['type']=='другое'?'active':'') ?>" data-type="другое">
        <i class="fa-solid fa-layer-group"></i> Другое
    </div>

 </div>

 <input type="hidden" name="type" id="typeInput" value="<?= $project['type'] ?>">

 <label>Количество комнат</label>
 <input type="number" name="num_rooms" min="1" required value="<?= $project['num_rooms'] ?>">

 <label>Количество этажей</label>
 <input type="number" name="num_floors" min="1" required value="<?= $project['num_floors'] ?>">

 <label>Учитывать бюджет?</label>
 <div class="budget-mode-box">
    <input type="checkbox" id="budgetMode" name="budget_mode" class="styled-checkbox" style="width: 28px;"
           <?= $project['budget_mode'] ? 'checked' : '' ?>>
    <label style="margin: 0;" for="budgetMode">Включить финансовый контроль</label>
 </div>

 <div class="budget-fields">
    <label>Общий бюджет (BYN)</label>
    <input type="number" step="0.01" name="budget_total"
           value="<?= $project['budget_total'] ?>"
           <?= $project['budget_mode'] ? '' : 'disabled' ?>>
 </div>

 <button class="profile-btn btn-login project-submit-btn" type="submit">
    Сохранить изменения
 </button>

 </form>

 </div>

 </div>
</section>

<?php include('../app/views/partials/footer.php'); ?>

<script src="./js/script.js"></script>

<script>
 const typeOptions = document.querySelectorAll('.type-option');
 const typeInput = document.getElementById('typeInput');

 typeOptions.forEach(option => {
    option.addEventListener('click', () => {
        typeOptions.forEach(o => o.classList.remove('active'));
        option.classList.add('active');
        typeInput.value = option.dataset.type;
    });
 });

 const budgetMode = document.getElementById('budgetMode');
 const budgetFields = document.querySelector('.budget-fields input');

 budgetMode.addEventListener('change', () => {
    budgetFields.disabled = !budgetMode.checked;
    if (!budgetMode.checked) budgetFields.value = 0;
 });
</script>

<?php unset($_SESSION['toast_message']); ?>

</body>
</html>
