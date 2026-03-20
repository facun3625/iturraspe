<?php
// Ruta: control_stock/backend/models/operatingCostModel.php

require_once __DIR__ . '/../config/database.php';

class OperatingCostModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addCost($category, $description, $amount, $date) {
        $query = "INSERT INTO operating_costs (category, description, amount, date) 
                  VALUES (:category, :description, :amount, :date)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':date', $date);
        return $stmt->execute();
    }

    public function getCostsByMonth($year, $month) {
        $query = "SELECT * FROM operating_costs 
                  WHERE YEAR(date) = :year AND MONTH(date) = :month 
                  ORDER BY date DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalByMonth($year, $month) {
        $query = "SELECT SUM(amount) as total FROM operating_costs 
                  WHERE YEAR(date) = :year AND MONTH(date) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function deleteCost($id) {
        $query = "DELETE FROM operating_costs WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
