<?php
// Archivo: control_stock/frontend/pages/priceUpdateConfirmation.php

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

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Precios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once '../components/navbar.php'; ?>
    <div class="rounded-container-form">
        <h4 class="text-center pb-4">Base de Datos Actualizada</h4>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Los precios se han actualizado correctamente.
            </div>
            <?php if (isset($_GET['updates'])): ?>
                <p class="text-center">Total de registros actualizados: <strong><?php echo htmlspecialchars($_GET['updates']); ?></strong></p>
            <?php endif; ?>
            <a href="../../backend/controllers/anotherAction.php" class="btn btn-danger">Actualizar Costos</a>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 2): ?>
            <div class="alert alert-success" role="alert">
                Sus costos han sido actualizados con éxito.
            </div>
            <?php if (isset($_GET['updates'])): ?>
                <p class="text-center">Total de registros actualizados: <strong><?php echo htmlspecialchars($_GET['updates']); ?></strong></p>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Ha ocurrido un error al actualizar los precios.
            </div>
            <a href="../../backend/controllers/anotherAction.php" class="btn btn-danger">Actualizar Costos</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
