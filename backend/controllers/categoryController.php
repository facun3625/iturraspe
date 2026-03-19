<?php
// Ruta: control_stock/backend/controllers/categoryController.php
require_once __DIR__ . '/../config/database.php';

class CategoryController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para agregar una nueva categoría
    public function addCategory($name, $description, $porcentaje) {
        $query = "INSERT INTO categories (name, description, porcentaje) VALUES (:name, :description, :porcentaje)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':porcentaje', $porcentaje);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function getCategoriesWithProductCount() {
        $query = "
            SELECT categories.id, categories.name, categories.description, 
                   COUNT(products.id) AS product_count,
                   categories.porcentaje  -- Asegúrate de incluir este campo
            FROM categories
            LEFT JOIN products ON products.category_id = categories.id
            GROUP BY categories.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function updateCategory($id, $name, $description, $porcentaje) {
        $query = "UPDATE categories SET name = :name, description = :description, porcentaje = :porcentaje WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':porcentaje', $porcentaje);
    
        return $stmt->execute();
    }
    
    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
    
        return $stmt->execute();
    }
    // Método para obtener todas las categorías
    public function getCategories() {
        $query = "SELECT id, name FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
