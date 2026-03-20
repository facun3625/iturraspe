<?php
// Ruta: control_stock/frontend/pages/updateProfitMargin.php

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

require_once '../../backend/controllers/productController.php';
$productController = new ProductController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMargin = $_POST['profit_margin'];

    // Actualizar el margen de ganancia en la base de datos
    if ($productController->updateProfitMargin($newMargin)) {
        $successMessage = "Margen de ganancia actualizado con éxito.";
    } else {
        $errorMessage = "Error al actualizar el margen de ganancia.";
    }
}

// Obtener el margen de ganancia actual
$currentMargin = $productController->getProfitMargin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Margen de Ganancia</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-form">
        <h4 class="text-center mb-4">Modificar Margen de Ganancia</h4>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success text-center"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger text-center"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="profit_margin">Margen de Ganancia (%)</label>
                <input type="number" step="0.01" name="profit_margin" id="profit_margin" class="form-control" value="<?php echo htmlspecialchars($currentMargin); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
