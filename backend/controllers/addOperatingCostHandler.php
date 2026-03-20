<?php
// Ruta: control_stock/backend/controllers/addOperatingCostHandler.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'operatingCostController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? 'Otros';
    $description = $_POST['description'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $date = $_POST['date'] ?? date('Y-m-d');

    $controller = new OperatingCostController();
    $success = $controller->addCost($category, $description, $amount, $date);

    if ($success) {
        header("Location: ../../frontend/pages/operatingCosts.php?success=1");
    } else {
        header("Location: ../../frontend/pages/operatingCosts.php?error=1");
    }
    exit();
}
