<?php
namespace App\Models;

use App\Core\Database;

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = new Database();
    }

    public function create($name, $email, $password) {
        $conn = $this->db->getConnection();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " SET name=:name, email=:email, password_hash=:password_hash";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $password_hash);
        
        return $stmt->execute();
    }

    public function findByEmail($email) {
        $conn = $this->db->getConnection();
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}