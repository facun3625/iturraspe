<?php
// Ruta: control_stock/backend/controllers/deleteProductHandler.php
require_once 'productController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productController = new ProductController();
    $result = $productController->activeProduct($id);

    if ($result) {
        header("Location: ../../frontend/pages/productListHide.php?delete_success=1");
    } else {
        header("Location: ../../frontend/pages/productListHide.php?delete_error=1");
    }
    exit();
}
