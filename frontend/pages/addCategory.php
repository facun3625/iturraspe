<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Ruta: control_stock/frontend/pages/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';

$authController = new AuthController();

// Verifica si el usuario está autenticado
if (!$authController->isLoggedIn()) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <?php include_once '../components/navbar.php'; ?>

    <!-- Contenedor Principal -->

    <div class="rounded-container-form">
       <!-- Contenedor más pequeño -->
            <h4 class="text-center pb-4">Agregar Nueva Categoría</h4>
            
            <!-- Mensaje de éxito o error con redirección para limpiar la URL -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success auto-hide">Categoría agregada con éxito.</div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'addCategory.php';
                    }, 1500);
                </script>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error al agregar la categoría.</div>
            <?php endif; ?>

            <form action="../../backend/controllers/addCategoryHandler.php" method="post">
    <div class="form-group">
        <label for="name">Nombre de la Categoría</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="porcentaje">Porcentaje de ganancia</label>
        <input type="number" class="form-control" id="porcentaje" name="porcentaje" step="0.01" min="0" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Agregar Categoría</button>
</form>
       
    </div>
    

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
