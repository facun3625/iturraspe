<?php
// Ruta: control_stock/backend/models/saleModel.php
require_once __DIR__ . '/../config/database.php';
class SaleModel {
    private $conn;
    public function __construct() { $database = new Database(); $this->conn = $database->getConnection(); }
    public function createSale($client_id, $total) {
        $query = "INSERT INTO sales (client_id, total, sale_date) VALUES (:client_id, :total, NOW())";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':client_id', $client_id); $stmt->bindParam(':total', $total);
        if ($stmt->execute()) return $this->conn->lastInsertId(); return false;
    }
    public function addSaleDetail($sale_id, $product_id, $quantity, $price, $discount = 0) {
        $query = "INSERT INTO sale_details (sale_id, product_id, quantity, price, discount) VALUES (:sale_id, :product_id, :quantity, :price, :discount)";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':sale_id', $sale_id); $stmt->bindParam(':product_id', $product_id); $stmt->bindParam(':quantity', $quantity); $stmt->bindParam(':price', $price); $stmt->bindParam(':discount', $discount);
        return $stmt->execute();
    }
}
