<?php
require '../../app/core/database.php';

use App\Core\Database;


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = Database::getInstance()->getConnection();
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        $_SESSION['toast_message'] = "Привет, " . $user['name'] . "!";

        header("Location: /public/index.php");
        exit;
    }

    $_SESSION['toast_message'] = "Неверный email или пароль";
    header("Location: /public/index.php");
    exit;
}
