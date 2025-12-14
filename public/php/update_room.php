<?php
session_start();
require '../../app/core/database.php';

use App\Core\Database;
$db = Database::getInstance()->getConnection();

$room_id = intval($_POST['room_id']);
$project_id = intval($_POST['project_id']);
$name = trim($_POST['name']);
$floor = intval($_POST['floor']);

if ($name === '') {
    echo "<script>alert('Название не может быть пустым'); window.history.back();</script>";
    exit;
}

$stmt = $db->prepare("SELECT num_floors FROM projects WHERE id=:pid");
$stmt->execute([':pid'=>$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) exit;

if ($floor < 1 || $floor > $project['num_floors']) {
    echo "<script>alert('Этаж должен быть от 1 до {$project['num_floors']}'); window.history.back();</script>";
    exit;
}

$stmt = $db->prepare("UPDATE rooms SET name=:n, floor=:f WHERE id=:id");
$stmt->execute([':n'=>$name, ':f'=>$floor, ':id'=>$room_id]);

header("Location: /public/project.php?id=".$project_id);
exit;
