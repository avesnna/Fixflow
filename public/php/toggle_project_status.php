<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$project_id || !in_array($action, ['finish','resume'])) {
    header("Location: /public/projects.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM projects WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $project_id, ':uid' => $_SESSION['user_id']]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: /public/projects.php");
    exit;
}


$hasStatusColumn = array_key_exists('status', $project);

if ($action === 'finish') {
    if ($hasStatusColumn) {
        
        if ($project['budget_mode']) {
            $stmt = $db->prepare("UPDATE projects SET  status = 'completed' WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        } else {
           
            $stmt = $db->prepare("UPDATE projects SET status = 'completed' WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        }
    } else {
        
        if ($project['budget_mode']) {
            $stmt = $db->prepare("UPDATE projects SET budget_spent = budget_total WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        } else {

            $stmt = $db->prepare("UPDATE projects SET budget_mode = 1, budget_total = 1, budget_spent = 1 WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        }
    }
}

if ($action === 'resume') {
    if ($hasStatusColumn) {

        if ($project['budget_mode']) {
            $stmt = $db->prepare("UPDATE projects SET status = 'active', budget_spent = 0 WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        } else {
            $stmt = $db->prepare("UPDATE projects SET status = 'active' WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        }
    } else {

        if ($project['budget_mode'] && floatval($project['budget_total']) == 1 && floatval($project['budget_spent']) == 1) {
            $stmt = $db->prepare("UPDATE projects SET budget_mode = 0, budget_total = 0, budget_spent = 0 WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        } else {

            $stmt = $db->prepare("UPDATE projects SET budget_spent = 0 WHERE id = :id");
            $stmt->execute([':id' => $project_id]);
        }
    }
}

header("Location: /public/projects.php");
exit;
