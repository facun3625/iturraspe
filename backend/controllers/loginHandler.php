<?php
// Ruta: control_stock/backend/controllers/loginHandler.php
session_start();
require_once 'authController.php';

$authController = new AuthController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($authController->login($username, $password)) {
        header("Location: ../../frontend/pages/dashboard.php"); // Redirige al dashboard si es exitoso
        exit();
    } else {
        header("Location: ../../frontend/pages/login2.php"); // Redirige a login2.php si los datos son incorrectos
        exit();
    }
}
?>
