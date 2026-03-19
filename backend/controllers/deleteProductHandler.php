<?php
// Ruta: control_stock/backend/controllers/deleteProductHandler.php
require_once 'productController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productController = new ProductController();
    $result = $productController->deleteProduct($id);

    if ($result) {
        header("Location: ../../frontend/pages/productList.php?delete_success=1");
    } else {
        header("Location: ../../frontend/pages/productList.php?delete_error=1");
    }
    exit();
}
