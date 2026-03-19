<?php
// Ruta: control_stock/backend/controllers/updateSaleHandler.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['sale_id']) || !isset($data['client_id']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit();
}

require_once 'saleController.php';
$saleController = new SaleController();

$result = $saleController->updateSale($data['sale_id'], $data);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la venta.']);
}
