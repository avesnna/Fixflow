<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("DELETE FROM projects WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $id, ':uid' => $user_id]);

header("Location: /public/projects.php");
exit;
