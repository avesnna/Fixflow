<?php
require '../../app/core/database.php';

use App\Core\Database;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    $name = $_POST['name'];
    $email = $_POST['email'];
    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $passwordHash);


        $stmt->execute();
$_SESSION['user_id'] = $db->lastInsertId();
$_SESSION['user_name'] = $name;
header("Location: /public/index.php");
exit;


}
