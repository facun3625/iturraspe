<?php
// Ruta: control_stock/backend/controllers/clientController.php

require_once __DIR__ . '/../config/database.php';

class ClientController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addClient($name, $phone, $email, $cuit, $address) {
        $query = "INSERT INTO clients (name, phone, email, cuit, address) VALUES (:name, :phone, :email, :cuit, :address)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cuit', $cuit);
        $stmt->bindParam(':address', $address);

        return $stmt->execute();
    }
    public function getClients() {
        $query = "SELECT * FROM clients";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateClient($id, $name, $phone, $email, $cuit, $address) {
        $query = "UPDATE clients SET name = :name, phone = :phone, email = :email, cuit = :cuit, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cuit', $cuit);
        $stmt->bindParam(':address', $address);
    
        return $stmt->execute();
    }
    
    public function deleteClient($id) {
        $query = "UPDATE clients SET is_active = 0 WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}

public function activeClient($id) {
    $query = "UPDATE clients SET is_active = 1 WHERE id = :id";
$stmt = $this->conn->prepare($query);
$stmt->bindParam(':id', $id);

return $stmt->execute();
}

    public function getClientById($id) {
        $query = "SELECT * FROM clients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getClientsWithDebt() {
        $query = "
            SELECT c.id, c.name, c.phone, c.email, c.cuit, c.address, 
                   COUNT(s.id) AS purchase_count, 
                   COALESCE(SUM(s.total - s.amount_paid), 0) AS debt
            FROM clients c
            LEFT JOIN sales s ON c.id = s.client_id
            WHERE c.is_active = 1  -- Solo clientes activos
            GROUP BY c.id, c.name, c.phone, c.email, c.cuit, c.address
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getClientsWithDebtHide() {
        $query = "
            SELECT c.id, c.name, COUNT(s.id) AS purchase_count, COALESCE(SUM(s.total - s.amount_paid), 0) AS debt
            FROM clients c
            LEFT JOIN sales s ON c.id = s.client_id
            WHERE c.is_active = 0  -- Solo clientes inactivos
            GROUP BY c.id, c.name
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getClientsWithDebtOnly() {
        $query = "
            SELECT c.id, c.name, c.phone, c.email, c.cuit, c.address, 
                   COUNT(s.id) AS purchase_count, 
                   COALESCE(SUM(s.total - s.amount_paid), 0) AS debt
            FROM clients c
            LEFT JOIN sales s ON c.id = s.client_id
            WHERE c.is_active = 1  -- Solo clientes activos
            GROUP BY c.id, c.name, c.phone, c.email, c.cuit, c.address
            HAVING debt > 0  -- Filtrar solo clientes con deuda
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
