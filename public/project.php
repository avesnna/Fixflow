<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    header("Location: /public/projects.php");
    exit;
}

$project_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM projects WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $project_id, ':uid' => $user_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: /public/projects.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM rooms WHERE project_id = :pid ORDER BY floor, name ASC");
$stmt->execute([':pid' => $project_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT a.*, r.name AS room_name, r.floor as room_floor
                      FROM actions a 
                      LEFT JOIN rooms r ON a.room_id = r.id
                      WHERE a.project_id = :pid
                      ORDER BY a.created_at DESC");
$stmt->execute([':pid' => $project_id]);
$actions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT 
        SUM(CASE WHEN status='запланировано' THEN cost END) AS planned,
        SUM(CASE WHEN status='в процессе' THEN cost END) AS in_progress,
        SUM(CASE WHEN status='выполнено' THEN cost END) AS done
    FROM actions
    WHERE project_id = :pid
");
$stmt->execute([':pid' => $project_id]);
$costs = $stmt->fetch(PDO::FETCH_ASSOC);

$planned = $costs['planned'] ?? 0;
$in_progress = $costs['in_progress'] ?? 0;
$done = $costs['done'] ?? 0;

$all_sum = $planned + $in_progress + $done;

$upd = $db->prepare("UPDATE projects SET budget_spent = :spent WHERE id = :pid");
$upd->execute([':spent' => $all_sum, ':pid' => $project_id]);

$project['budget_spent'] = $all_sum;

$stmt = $db->prepare("SELECT SUM(cost) FROM actions WHERE project_id = :pid"); $stmt->execute([':pid' => $project_id]); $total_cost = $stmt->fetchColumn() ?? 0;

 $upd = $db->prepare("UPDATE projects SET budget_spent = :spent WHERE id = :pid"); $upd->execute([':spent' => $total_cost, ':pid' => $project_id]); $project['budget_spent'] = $total_cost;


if ($project['budget_mode'] && $project['budget_total'] > 0 && $total_cost >= $project['budget_total']) {
    $db->prepare("UPDATE projects SET status='completed' WHERE id=:id")
       ->execute([':id' => $project_id]);
    $project['status'] = 'completed';
}

$icon = [
    'квартира' => 'fa-building',
    'частный дом' => 'fa-house-chimney',
    'другое' => 'fa-layer-group'
][$project['type']];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($project['title']) ?> — FixFlow</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg">

  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>

<?php include('../app/views/partials/header.php'); ?>

<section class="hero fade-in">
    <div class="container hero-inner">
      <div class="hero-icon"><i class="fa-solid <?= $icon ?>"></i></div>
      <h1><?= htmlspecialchars($project['title']) ?></h1>
      <p class="hero-sub">Полная информация о проекте</p>
    </div>
</section>

<section class="about fade-in">

 <nav class="breadcrumbs fade-in">
    <div class="container">
        <a href="/public/profile.php">Мой профиль</a>
        <span>/</span>
        <a href="/public/projects.php">Мои проекты</a>
        <span>/</span>
        <span class="current"><?= htmlspecialchars($project['title']) ?></span>
    </div>
 </nav>

 <div class="container">

 <div class="project-card multi">
    <div class="info-header">
        <h2><i class="fa-solid fa-info-circle"></i> Общая информация</h2>
    </div>

    <div class="project-info-grid">
    <span class="project-type"><?= $project['type'] ?></span>
        <p><strong>Комнат:</strong> <?= $project['num_rooms'] ?></p>
        <p><strong>Этажей:</strong> <?= $project['num_floors'] ?></p>
        <p><strong>Статус:</strong> 
           <?= $project['status'] === 'completed' ? 'Завершён' : 'Активный' ?>
        </p>
    </div>
    <div class="info-actions">
        <div class="project-card-actions">
        <a href="/public/edit-project.php?id=<?= $project['id'] ?>" class="action-btn edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="/public/php/delete_project.php" method="POST" onsubmit="return confirm('Удалить проект?');">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
            <button type="submit" class="action-btn delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
         </div>

            <form method="POST" action="/public/php/toggle_project_status.php">
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                <?php if ($project['status'] === 'completed'): ?>
                    <button class="project-btn green" name="action" value="resume">
                        <i class="fa-solid fa-rotate-left"></i> Возобновить
                    </button>
                <?php else: ?>
                    <button class="project-btn red" name="action" value="finish">
                        <i class="fa-solid fa-check"></i> Завершить
                    </button>
                <?php endif; ?>
            </form>
        </div>
 </div>

 <div class="project-card multi fade-in">
  <div class="info-header">
    <h2><i class="fa-solid fa-wallet"></i> Финансы</h2>
 </div>

 <?php if ($project['budget_mode']): ?>

    <p><strong>Общая сумма:</strong> <?= $all_sum ?> / <?= $project['budget_total'] ?> BYN</p>

    
    <p style="margin:6px 0 4px;">Запланировано (<?= $planned ?>)</p>
    <div class="progress-bar small">
        <div class="progress blue" style="width: <?= $project['budget_total'] > 0 ? ($planned / $project['budget_total'])*100 : 0 ?>%"></div>
    </div>

    
    <p style="margin:6px 0 4px;">В процессе (<?= $in_progress ?>)</p>
    <div class="progress-bar small">
        <div class="progress yellow" style="width: <?= $project['budget_total'] > 0 ? ($in_progress / $project['budget_total'])*100 : 0 ?>%"></div>
    </div>

    
    <p style="margin:6px 0 4px;">Выполнено (<?= $done ?>)</p>
    <div class="progress-bar small">
        <div class="progress green" style="width: <?= $project['budget_total'] > 0 ? ($done / $project['budget_total'])*100 : 0 ?>%"></div>
    </div>

 <?php else: ?>
    <p>Финансовый контроль выключен</p>
 <?php endif; ?>

 </div>

 <div class="project-card multi">
    <div class="info-header">
        <h2><i class="fa-solid fa-door-open"></i> Комнаты (<?= $project['num_rooms'] ?>)</h2>
    </div>

    <?php if (empty($rooms)): ?>
        <p class="empty-text">Комнат пока нет</p>
    <?php else: ?>
        <div class="rooms-list">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <div class="room-card-one">
                    <i class="fa-solid fa-door-closed"></i>
                    <h3><?= htmlspecialchars($room['name']) ?> <span>(этаж: <?= $room['floor'] ?>)</span></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
  <div class="buttons-row">
    <button type="button" id="showRoomForm" class="add-btn">
    <i class="fa-solid fa-plus"></i> Добавить комнату
    </button>
    <button class="add-btn" id="editRoomsBtn">
    <i class="fa-solid fa-pen"></i> Изменить список комнат
    </button>
  </div>

  <div id="roomForm" class="hidden room-form-box">
    <form action="/public/php/add_room_inline.php" method="POST">
        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

        <label>Название комнаты</label>
        <input type="text" name="name" required placeholder="Например: Кухня">

        <label>Этаж</label>
        <input type="number" min="1" max="<?= $project['num_floors'] ?>" 
               name="floor" value="1" required>

        <button class="project-btn green" type="submit" style="margin-top: 15px;">
            <i class="fa-solid fa-check"></i> Сохранить
        </button>
    </form>
  </div>

  <div id="roomsEditor" style="display:none; margin-top:20px;" class="inputs-main">
    <h3 style="margin-bottom:10px;">Редактирование комнат</h3>

    <?php foreach ($rooms as $room): ?>
        <div class="room-edit-row" style="
            padding:12px; 
            margin-bottom:10px;
            display:flex;
            gap:15px;
            align-items:center; ">
            <form action="/public/php/update_room.php" method="POST" class="inputs" style="display:flex; gap:10px; width:100%; flex-wrap:wrap">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <input type="hidden" name="project_id" value="<?= $project_id ?>">

                <input type="text" name="name" value="<?= htmlspecialchars($room['name']) ?>" 
                       class="input" style="flex:2;">

                <input type="number" name="floor" value="<?= $room['floor'] ?>" 
                       min="1" max="<?= $project['num_floors'] ?>" 
                       class="input" style="width:100px;">

                <button class="project-btn green" style="padding:10px 16px;">
                    <i class="fa-solid fa-check"></i>
                </button>
                
            </form>
            <form action="/public/php/delete_room.php" method="POST" 
                  onsubmit="return confirm('Удалить комнату?');" class="delete">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <input type="hidden" name="project_id" value="<?= $project_id ?>">

                <button class="project-btn red" style="padding:10px 16px;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
            
        </div>
    <?php endforeach; ?>
  </div>

 </div>

 <div class="project-card multi">
    <div class="info-header">
        <h2><i class="fa-solid fa-list-check"></i> Задачи (<?= count($actions) ?>)</h2>
    </div>

    <?php if (empty($actions)): ?>
        <p class="empty-text">Задач пока нет</p>
    <?php else: ?>
        <div class="actions-list">
            <?php foreach ($actions as $task): ?>
                <div class="action-card">
                    <div class="action-header">
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <span class="task-status <?= str_replace(' ', '-', $task['status'])?>"><?= $task['status'] ?></span>
                            <p><?= $task['description'] ?></p>
                    </div>
                    <div class="room-card">
                        <div class="room-card-one">
                    <?php if ($task['room_name']): ?>
                        <h3><i class="fa-solid fa-door-closed"></i> <?= htmlspecialchars($task['room_name'] . " (Этаж: " . $task['room_floor'] . ")") ?></h3>
                        <?php else: ?>
                        <h3>Проект</h3>       
                    <?php endif; ?>
                    </div>
                    </div>

                    <p><strong>Стоимость:</strong> <?= $task['cost'] ?> BYN</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="buttons-row">
        <button type="button" id="showTaskForm" class="add-btn">
            <i class="fa-solid fa-plus"></i> Добавить задачу
        </button>
        <button class="add-btn" id="editTasksBtn">
            <i class="fa-solid fa-pen"></i> Изменить список задач
        </button>
    </div>

    
    <div id="taskForm" class="hidden task-form-box">
        <form action="/public/php/add_action_inline.php" method="POST">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

            <label>Название задачи</label>
            <input type="text" name="title" required placeholder="Например: Купить люстру">

            <label>Описание</label>
            <textarea name="description" placeholder="Описание задачи"></textarea>

            <label>Стоимость (BYN)</label>
            <input type="number" step="0.01" min="0" name="cost" value="0">

            <label>Комната</label>
            <select name="room_id">
                <option value="">Не привязана</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>">
                    <?= htmlspecialchars($room['name'] . " (этаж: " . $room['floor'] . ")") ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Статус</label>
            <select name="status">
                <option value="запланировано">Запланировано</option>
                <option value="в процессе">В процессе</option>
                <option value="выполнено">Выполнено</option>
            </select>

            <button class="project-btn green" type="submit" style="margin-top: 15px;">
                <i class="fa-solid fa-check"></i> Сохранить
            </button>
        </form>
    </div>

   
    <div id="tasksEditor" style="display:none; margin-top:20px;" class="inputs-main">
        <h3 style="margin-bottom:10px;">Редактирование задач</h3>

        <?php foreach ($actions as $task): ?>
            <div class="action-edit-row" style="
                padding:12px; 
                margin-bottom:10px;
                display:flex;
                gap:15px;
                align-items:center;
                flex-wrap: wrap;">
                
                <form action="/public/php/update_action.php" method="POST" class="inputs" style="display:flex; gap:10px; width:100%; flex-wrap:wrap;">
                    <input type="hidden" name="action_id" value="<?= $task['id'] ?>">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" class="input" style="flex:2;">
                    <textarea name="description" class="input" style="width:100%;" placeholder="Описание задачи"><?= htmlspecialchars($task['description']) ?></textarea>

                    <input type="number" name="cost" value="<?= $task['cost'] ?>" step="0.01" class="input">
                    
                    <select name="status" class="input">
                        <option value="запланировано" <?= $task['status'] === 'запланировано' ? 'selected' : '' ?>>Запланировано</option>
                        <option value="в процессе" <?= $task['status'] === 'в процессе' ? 'selected' : '' ?>>В процессе</option>
                        <option value="выполнено" <?= $task['status'] === 'выполнено' ? 'selected' : '' ?>>Выполнено</option>
                    </select>

                    <select name="room_id" class="input">
                        <option value="">Не привязана</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= $room['id'] ?>" <?= $task['room_id'] == $room['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room['name'] . " (этаж: " . $room['floor'] . ")")  ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button class="project-btn green" style="padding:10px 16px;">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </form>

                <form action="/public/php/delete_action.php" method="POST" onsubmit="return confirm('Удалить задачу?');" class="delete">
                    <input type="hidden" name="action_id" value="<?= $task['id'] ?>">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                    <button class="project-btn red" style="padding:10px 16px;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
 </div>

 </div>
</section>

<?php include('../app/views/partials/footer.php'); ?>

<script src="./js/script.js"></script>

<script>
 const roomForm = document.getElementById("roomForm");
 const roomsEditor = document.getElementById("roomsEditor");
 const btnAddRoom = document.getElementById("showRoomForm");
 const btnEditRooms = document.getElementById("editRoomsBtn");
 const taskForm = document.getElementById("taskForm");
 const tasksEditor = document.getElementById("tasksEditor");
 const btnAddTask = document.getElementById("showTaskForm");
 const btnEditTasks = document.getElementById("editTasksBtn");

 function closeAllTasks() {
    if (taskForm && !taskForm.classList.contains("hidden")) {
        taskForm.classList.add("hidden");
    }
    if (tasksEditor && tasksEditor.style.display !== "none") {
        tasksEditor.style.display = "none";
    }
 }

 btnAddTask?.addEventListener("click", () => {
    if (tasksEditor) tasksEditor.style.display = "none";
    taskForm?.classList.toggle("hidden");
 });

 btnEditTasks?.addEventListener("click", () => {
    if (taskForm) taskForm.classList.add("hidden");
    if (tasksEditor) {
        tasksEditor.style.display = tasksEditor.style.display === "none" ? "block" : "none";
    }
 });

 function closeAll() {
    if (roomForm && !roomForm.classList.contains("hidden")) {
        roomForm.classList.add("hidden");
    }
    if (roomsEditor && roomsEditor.style.display !== "none") {
        roomsEditor.style.display = "none";
    }
 }

 btnAddRoom?.addEventListener("click", () => {
   
    if (roomsEditor) roomsEditor.style.display = "none";

    roomForm?.classList.toggle("hidden");
 });


 btnEditRooms?.addEventListener("click", () => {

    if (roomForm) roomForm.classList.add("hidden");

    if (roomsEditor) {
        roomsEditor.style.display =
            roomsEditor.style.display === "none" ? "block" : "none";
    }
 });
</script>

</body>
</html>
