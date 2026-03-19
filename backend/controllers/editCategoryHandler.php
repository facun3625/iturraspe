<?php
// Ruta: control_stock/backend/controllers/editCategoryHandler.php

require_once 'categoryController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $porcentaje = $_POST['porcentaje']; // Obtener el porcentaje del formulario

    $categoryController = new CategoryController();
    $result = $categoryController->updateCategory($id, $name, $description, $porcentaje);

    if ($result) {
        header("Location: ../../frontend/pages/categoryList.php?edit_success=1");
    } else {
        header("Location: ../../frontend/pages/categoryList?edit_error=1");
    }
    exit();
}
