<?php
// Ruta: control_stock/backend/controllers/removeStockHandler.php
require_once 'productController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $amount = $_POST['amount'];

    $productController = new ProductController();
    $result = $productController->removeStock($id, $amount);

    if ($result) {
        header("Location: ../../frontend/pages/productList.php?remove_stock_success=1");
    } else {
        header("Location: ../../frontend/pages/productList.php?remove_stock_error=1");
    }
    exit();
}
