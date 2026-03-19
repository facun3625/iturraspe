<?php
// Ruta: control_stock/backend/models/saleModel.php

require_once '../config/database.php';

class SaleModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear una nueva venta
    public function createSale($client_id, $total) {
        $query = "INSERT INTO sales (client_id, total) VALUES (:client_id, :total)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Agregar detalles de venta
    public function addSaleDetail($sale_id, $product_id, $quantity, $price) {
        $query = "INSERT INTO sale_details (sale_id, product_id, quantity, price) VALUES (:sale_id, :product_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->execute();
    }

    // Reducir stock del producto
    public function updateProductStock($product_id, $quantity_sold) {
        $query = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity_sold);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
    }
}
