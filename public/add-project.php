<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $num_rooms = intval($_POST['num_rooms']);
    $num_floors = intval($_POST['num_floors']);
    $budget_mode = isset($_POST['budget_mode']) ? 1 : 0;
    $budget_total = $budget_mode ? floatval($_POST['budget_total']) : 0;

    if ($title === '') {
        $_SESSION['toast_message'] = "Название проекта не может быть пустым";
        header("Location: /public/add-project.php");
        exit;
    }

    $stmt = $db->prepare("
        INSERT INTO projects 
        (user_id, title, type, num_rooms, num_floors, budget_mode, budget_total, budget_spent)
        VALUES (:user_id, :title, :type, :num_rooms, :num_floors, :budget_mode, :budget_total, 0)
    ");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':title'   => $title,
        ':type'    => $type,
        ':num_rooms' => $num_rooms,
        ':num_floors' => $num_floors,
        ':budget_mode' => $budget_mode,
        ':budget_total' => $budget_total
    ]);

    $_SESSION['toast_message'] = "Проект успешно создан!";
    header("Location: /public/projects.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Добавить проект — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">
  
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid fa-circle-plus"></i></div>
      <h1>Добавить проект</h1>
      <p class="hero-sub">Создайте новый ремонтный проект</p>
    </div>
</section>

<section class="about fade-in">
 <nav class="breadcrumbs fade-in">
    <div class="container">
        <a href="/public/profile.php">Мой профиль</a>
        <span>/</span>
        <a href="/public/projects.php">Мои проекты</a>
        <span>/</span>
        <span class="current">Добавить проект</span>
    </div>
 </nav>

 <div class="container">
   

 <div class="project-form-card">
    <h2>Новый проект</h2>

 <form method="POST" class="project-form">

   <label>Название проекта</label>
   <input type="text" name="title" required placeholder="Например: Квартира ул. Победы 12а">

    <label>Тип объекта</label>
    <div class="project-type-select">
    <div class="type-option active" data-type="квартира">
        <i class="fa-solid fa-building"></i>
        Квартира
    </div>

    <div class="type-option" data-type="частный дом">
        <i class="fa-solid fa-house-chimney"></i>
        Частный дом
    </div>

    <div class="type-option" data-type="другое">
        <i class="fa-solid fa-layer-group"></i>
        Другое
    </div>
    </div>

    <input type="hidden" name="type" id="typeInput" value="квартира">

    <label>Количество комнат</label>
    <p style="margin-top: 5px; opacity:0.5; margin-bottom:0">* желательно указывать все комнаты, в которых будет проводиться ремонт
    (учитывая ванную, уборную, кухню, гардеробную и т.д.)
    </p>
    <input type="number" name="num_rooms" min="1" value="1" required>

    <label>Количество этажей</label>
    <input type="number" name="num_floors" min="1" value="1" required>

    <label>Учитывать бюджет?</label>
    <div class="budget-mode-box">
    <input type="checkbox" id="budgetMode" name="budget_mode" class="styled-checkbox" style="width: 28px;" >
    <label for="budgetMode" style="margin: 0;">Включить финансовый контроль</label>
    </div>

    <div class="budget-fields">
    <label>Общий планируемый бюджет (BYN)</label>
    <input type="number" step="0.01" name="budget_total" value="0" disabled placeholder="Введите сумму">
    </div>

    <button class="profile-btn btn-login project-submit-btn" type="submit">
    Создать проект
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

<?php unset($_SESSION['toast_message']);  ?>

</body>
</html>
