<?php
// Ruta: control_stock/backend/controllers/deleteOperatingCostHandler.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'operatingCostController.php';

if (isset($_GET['id'])) {
    $controller = new OperatingCostController();
    $controller->deleteCost($_GET['id']);
}

header("Location: ../../frontend/pages/operatingCosts.php?deleted=1");
exit();
