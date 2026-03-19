<?php
// Ruta: control_stock/backend/controllers/deleteCategoryHandler.php
require_once 'categoryController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $categoryController = new CategoryController();
    $result = $categoryController->deleteCategory($id);

    if ($result) {
        header("Location: ../../frontend/pages/categoryList.php?delete_success=1");
    } else {
        header("Location: ../../frontend/pages/categoryList.php?delete_error=1");
    }
    exit();
}
