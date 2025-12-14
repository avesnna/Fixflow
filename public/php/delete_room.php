<?php
session_start();
require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$room_id = intval($_POST['room_id']);
$project_id = intval($_POST['project_id']);

$stmt = $db->prepare("DELETE FROM rooms WHERE id = :id");
$stmt->execute([':id' => $room_id]);

header("Location: /public/project.php?id=".$project_id);
exit;
