<?php
// Ruta: control_stock/backend/controllers/finalizeSaleHandler.php

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/saleModel.php'; // Modelo para manejar las operaciones de venta

header('Content-Type: application/json');

// Leer los datos JSON enviados desde el frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['client_id']) || !isset($data['total']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'error' => 'Datos de venta incompletos']);
    exit;
}

$client_id = $data['client_id'];
$total = $data['total'];
$items = $data['items'];

try {
    $saleModel = new SaleModel();

    // 1. Crear registro de venta
    $sale_id = $saleModel->createSale($client_id, $total);
    
    // 2. Insertar cada detalle de venta y actualizar el stock
    foreach ($items as $item) {
        $saleModel->addSaleDetail($sale_id, $item['id'], $item['quantity'], $item['price']);
        $saleModel->updateProductStock($item['id'], $item['quantity']);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

