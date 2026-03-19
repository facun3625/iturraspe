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
}
