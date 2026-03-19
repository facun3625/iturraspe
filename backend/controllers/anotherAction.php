<?php
// Archivo: control_stock/frontend/pages/anotherAction.php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'productController.php'; // Ajusta la ruta según tu estructura

$productController = new ProductController();
$productController->updateCosts(); // Llama a la función que actualiza los costos

// Redirigir a la página de confirmación
header("Location: ../../frontend/pages/priceUpdateConfirmation.php?success=2");
exit();
?>
