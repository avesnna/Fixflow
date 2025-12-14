<?php
namespace App\Core;

class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db_name = 'fixflow';
    private $username = 'root'; 
    private $password = '1389root';

    private function __construct() {
        try {
            $this->conn = new \PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }

    // Единственная точка доступа к объекту
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
