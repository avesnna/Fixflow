<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$project_id = intval($_POST['project_id']);
$name = trim($_POST['name']);
$floor = intval($_POST['floor']);

if ($name === '') {
    $_SESSION['error'] = "Название комнаты не может быть пустым.";
    header("Location: /public/project.php?id=" . $project_id);
    exit;
}


$stmt = $db->prepare("SELECT num_floors, num_rooms, user_id FROM projects WHERE id = :id");
$stmt->execute([':id' => $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || $project['user_id'] != $_SESSION['user_id']) {
    header("Location: /public/projects.php");
    exit;
}


$stmt = $db->prepare("SELECT COUNT(*) FROM rooms WHERE project_id = :pid");
$stmt->execute([':pid' => $project_id]);
$current_rooms = $stmt->fetchColumn();

if ($current_rooms >= $project['num_rooms']) {
    echo "<script>
        alert('Нельзя добавить более {$project['num_rooms']} комнат.');
        window.location.href = '/public/project.php?id={$project_id}';
    </script>";
    exit;
}


if ($floor < 1 || $floor > $project['num_floors']) {
    $_SESSION['error'] = "Этаж должен быть от 1 до " . $project['num_floors'];
    header("Location: /public/project.php?id=" . $project_id);
    exit;
}

$stmt = $db->prepare("
    INSERT INTO rooms (project_id, name, floor)
    VALUES (:pid, :name, :floor)
");
$stmt->execute([
    ':pid' => $project_id,
    ':name' => $name,
    ':floor' => $floor
]);

header("Location: /public/project.php?id=" . $project_id);
exit;
