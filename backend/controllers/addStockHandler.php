<?php
// Ruta: control_stock/backend/controllers/addStockHandler.php
require_once 'productController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $amount = $_POST['amount'];

    $productController = new ProductController();
    $result = $productController->addStock($id, $amount);

    if ($result) {
        header("Location: ../../frontend/pages/productList.php?add_stock_success=1");
    } else {
        header("Location: ../../frontend/pages/productList.php?add_stock_error=1");
    }
    exit();
}
