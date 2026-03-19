<?php
// Ruta: control_stock/backend/config/database.php

class Database {
    private $host = "db";
    private $db_name = "distji_s";
    private $username = "distji_s"; // Cambia este dato si usas otro usuario
    private $password = "J*OqzaQ4fW";     // Cambia este dato si tienes contraseña configurada
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
