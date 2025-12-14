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

$stmt = $db->prepare("SELECT user_id FROM projects WHERE id = :id");
$stmt->execute([':id' => $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || $project['user_id'] != $_SESSION['user_id']) {
    header("Location: /public/projects.php");
    exit;
}

$stmt = $db->prepare("DELETE FROM actions WHERE id = :action_id");
$stmt->execute([':action_id' => $action_id]);

header("Location: /public/project.php?id=" . $project_id);
exit;
