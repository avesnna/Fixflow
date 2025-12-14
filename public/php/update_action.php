<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$action_id = intval($_POST['action_id']);
$project_id = intval($_POST['project_id']);
$title = trim($_POST['title']);
$description = trim($_POST['description'] ?? '');
$cost = floatval($_POST['cost'] ?? 0);
$status = $_POST['status'] ?? 'запланировано';
$room_id = !empty($_POST['room_id']) ? intval($_POST['room_id']) : null;

if ($title === '') {
    $_SESSION['error'] = "Название задачи не может быть пустым.";
    header("Location: /public/project.php?id=" . $project_id);
    exit;
}

$stmt = $db->prepare("SELECT user_id FROM projects WHERE id = :id");
$stmt->execute([':id' => $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || $project['user_id'] != $_SESSION['user_id']) {
    header("Location: /public/projects.php");
    exit;
}

if ($room_id) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM rooms WHERE id = :rid AND project_id = :pid");
    $stmt->execute([':rid' => $room_id, ':pid' => $project_id]);
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['error'] = "Выбранная комната не найдена в проекте.";
        header("Location: /public/project.php?id=" . $project_id);
        exit;
    }
}

$stmt = $db->prepare("
    UPDATE actions
    SET title = :title,
        description = :desc,
        cost = :cost,
        status = :status,
        room_id = :rid
    WHERE id = :action_id
");
$stmt->execute([
    ':title' => $title,
    ':desc' => $description,
    ':cost' => $cost,
    ':status' => $status,
    ':rid' => $room_id,
    ':action_id' => $action_id
]);

header("Location: /public/project.php?id=" . $project_id);
exit;
