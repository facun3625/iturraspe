<?php
// Ruta: control_stock/backend/controllers/addCategoryHandler.php
require_once 'categoryController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $porcentaje = $_POST['porcentaje']; // Obtener el porcentaje del formulario

    $categoryController = new CategoryController();
    $result = $categoryController->addCategory($name, $description, $porcentaje);

    if ($result) {
        header("Location: ../../frontend/pages/addCategory.php?success=1");
    } else {
        header("Location: ../../frontend/pages/addCategory.php?error=1");
    }
    exit();
}
