<?php
// Ruta: control_stock/backend/controllers/authController.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($inputUser, $inputPass) {
        $query = "SELECT * FROM users WHERE username = :username AND password = MD5(:password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $inputUser);
        $stmt->bindParam(':password', $inputPass);
        $stmt->execute();

        // Si se encuentra un usuario con esas credenciales
        if ($stmt->rowCount() == 1) {
            $_SESSION['username'] = $inputUser;
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['username']);
    }

    public function updatePassword($username, $newPassword) {
        $query = "UPDATE users SET password = MD5(:password) WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $newPassword);
        $stmt->bindParam(':username', $username);
        return $stmt->execute();
    }

    public function createUser($username, $password) {
        $query = "INSERT INTO users (username, password) VALUES (:username, MD5(:password))";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
