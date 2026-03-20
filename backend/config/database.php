<?php
// Ruta: control_stock/backend/config/database.php
class Database {
    private $host = "db";
    private $db_name = "distji_s";
    private $username = "distji_s";
    private $password = "J*OqzaQ4fW";
    public $conn;
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) { echo "Error de conexión: " . $exception->getMessage(); }
        return $this->conn;
    }
}
