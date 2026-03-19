<?php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta: control_stock/backend/controllers/productController.php
require_once __DIR__ . '/../config/database.php';

class ProductController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Agregar un nuevo producto
public function addProduct($id, $category_id, $name, $description, $stock, $low_stock_level, $cost, $list_price, $discount1, $discount2, $discount3, $discount4, $image_url) {
    $query = "INSERT INTO products (cod, category_id, name, description, stock, low_stock_level, cost, list_price, discount1, discount2, discount3, discount4, image_url) 
              VALUES (:cod, :category_id, :name, :description, :stock, :low_stock_level, :cost, :list_price, :discount1, :discount2, :discount3, :discount4, :image_url)";
    $stmt = $this->conn->prepare($query);

    // Asignar los parámetros
    $stmt->bindParam(':cod', $id);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':low_stock_level', $low_stock_level);
    $stmt->bindParam(':cost', $cost);
    $stmt->bindParam(':list_price', $list_price);
    $stmt->bindParam(':discount1', $discount1);
    $stmt->bindParam(':discount2', $discount2);
    $stmt->bindParam(':discount3', $discount3);
    $stmt->bindParam(':discount4', $discount4);
    $stmt->bindParam(':image_url', $image_url);

    return $stmt->execute();
}

    // Obtener todos los productos con el nombre de la categoría
    public function getProductsWithCategory() {
    $query = "SELECT products.id, products.name, categories.name AS category_name, products.stock, products.cost, products.price, products.image_url 
              FROM products 
              JOIN categories ON products.category_id = categories.id";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Actualizar un producto
public function updateProduct($id, $cod, $name, $category_id, $stock, $cost, $list_price, $discount1 = null, $discount2 = null, $discount3 = null, $discount4 = null, $imagePath = null) {
    $query = "UPDATE products SET cod = :cod, name = :name, category_id = :category_id, stock = :stock, cost = :cost, list_price = :list_price";

    // Agregar descuentos si no son null
    if ($discount1 !== null) {
        $query .= ", discount1 = :discount1";
    }
    if ($discount2 !== null) {
        $query .= ", discount2 = :discount2";
    }
    if ($discount3 !== null) {
        $query .= ", discount3 = :discount3";
    }
    if ($discount4 !== null) {
        $query .= ", discount4 = :discount4";
    }

    // Agrega image_url al query si hay una nueva imagen
    if ($imagePath !== null) {
        $query .= ", image_url = :image_url";
    }

    $query .= " WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    // Asignar parámetros
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':cod', $cod); // Aseguramos el parámetro 'cod'
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':cost', $cost);
    $stmt->bindParam(':list_price', $list_price);

    // Solo enlaza descuentos si se proporciona un nuevo valor
    if ($discount1 !== null) {
        $stmt->bindParam(':discount1', $discount1);
    }
    if ($discount2 !== null) {
        $stmt->bindParam(':discount2', $discount2);
    }
    if ($discount3 !== null) {
        $stmt->bindParam(':discount3', $discount3);
    }
    if ($discount4 !== null) {
        $stmt->bindParam(':discount4', $discount4);
    }

    // Solo enlaza image_url si se proporciona un nuevo valor
    if ($imagePath !== null) {
        $stmt->bindParam(':image_url', $imagePath);
    }

    return $stmt->execute();
}



    // Eliminar un producto
    public function deleteProduct($id) {
        $query = "UPDATE products SET is_active = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
    
        return $stmt->execute();
    }

    // Eliminar un producto
    public function activeProduct($id) {
        $query = "UPDATE products SET is_active = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
    
        return $stmt->execute();
    }

    // Agregar stock
    public function addStock($id, $amount) {
        $query = "UPDATE products SET stock = stock + :amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':amount', $amount);

        return $stmt->execute();
    }

    // Quitar stock
    public function removeStock($id, $amount) {
        $query = "UPDATE products SET stock = stock - :amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':amount', $amount);

        return $stmt->execute();
    }

    // Obtener un producto por ID
    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProducts() {
        // Obtener el margen de ganancia desde la tabla `settings`
        $query = "SELECT value FROM settings WHERE name = 'profit_margin'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $profitMargin = $stmt->fetch(PDO::FETCH_COLUMN);
    
        // Convertir el margen de ganancia a un decimal (por ejemplo, 30% -> 1.3)
        $profitMultiplier = 1 + ($profitMargin / 100);
    
        // Consulta para obtener los productos
        $query = "SELECT products.id, products.name, products.stock, products.cost, categories.name AS category_name 
                  FROM products 
                  JOIN categories ON products.category_id = categories.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Calcular el precio con el margen de ganancia
        foreach ($products as &$product) {
            $product['price'] = $product['cost'] * $profitMultiplier;
        }
    
        return $products;
    }
    // Método para obtener todas las categorías
    public function getAllCategories() {
        $query = "SELECT id, name FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProductsWithCalculatedPrice() {
        // Consulta para obtener los productos con sus datos, la URL de la imagen, y el porcentaje de la categoría, solo activos
        $query = "
            SELECT products.id, products.cod, products.name, categories.name AS category_name, 
                   products.stock, products.low_stock_level, products.cost, products.image_url, 
                   categories.porcentaje AS category_percentage  -- Incluir el porcentaje de la categoría
            FROM products 
            JOIN categories ON products.category_id = categories.id 
            WHERE products.is_active = 1"; // Solo productos activos
            
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Calcular el precio basado en el porcentaje de la categoría
        foreach ($products as &$product) {
            // Calcular el multiplicador basado en el porcentaje de la categoría
            $categoryMultiplier = 1 + ($product['category_percentage'] / 100);
            // Calcular el precio total
            $product['price'] = $product['cost'] * $categoryMultiplier;  // Precio calculado
        }
    
        return $products;
    }
    
    


    public function getProductsWithCalculatedPriceHide() {
        // Consulta para obtener los productos con sus datos y el porcentaje de la categoría, solo activos
        $query = "
            SELECT products.id, products.cod, products.name, categories.name AS category_name, 
                   products.stock, products.cost, products.image_url, 
                   categories.porcentaje AS category_percentage  -- Incluir el porcentaje de la categoría
            FROM products 
            JOIN categories ON products.category_id = categories.id 
            WHERE products.is_active = 0"; // Solo productos activos
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Calcular el precio basado en el porcentaje de la categoría
        foreach ($products as &$product) {
            // Calcular el multiplicador basado en el porcentaje de la categoría
            $categoryMultiplier = 1 + ($product['category_percentage'] / 100);  // Multiplicador
            // Calcular el precio total
            $product['price'] = $product['cost'] * $categoryMultiplier;  // Precio calculado
        }
    
        return $products;
    }
    
    
    public function getProductsWithImages() {
    $query = "SELECT products.id, products.name, categories.name AS category_name, products.stock, products.cost, products.price, products.image_url 
              FROM products 
              JOIN categories ON products.category_id = categories.id";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getProfitMargin() {
    $query = "SELECT value FROM settings WHERE name = 'profit_margin'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

public function updateProfitMargin($newMargin) {
    $query = "UPDATE settings SET value = :value WHERE name = 'profit_margin'";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':value', $newMargin);
    return $stmt->execute();
}
public function updatePriceByCode($cod, $new_price_list) {
    $query = "UPDATE products SET list_price = :list_price WHERE cod = :cod";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':list_price', $new_price_list);
    $stmt->bindParam(':cod', $cod);

    return $stmt->execute();
}



public function updateCosts() {
    $query = "SELECT cod, list_price, discount1, discount2, discount3, discount4 FROM products";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list_price = $row['list_price'];
        $discount1 = $row['discount1'];
        $discount2 = $row['discount2'];
        $discount3 = $row['discount3'];
        $discount4 = $row['discount4'];

        // Calcular el costo
        $cost = ceil($list_price * $discount1 * $discount2 * $discount3 * $discount4);

        // Actualizar el costo en la base de datos
        $this->updateCostByCode($row['cod'], $cost);
    }
}

private function updateCostByCode($cod, $cost) {
    $query = "UPDATE products SET cost = :cost WHERE cod = :cod";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':cost', $cost);
    $stmt->bindParam(':cod', $cod);

    return $stmt->execute();
}

public function getLowStockProducts() {
    $query = "SELECT products.id, products.cod, products.name, categories.name AS category_name, 
              products.stock, products.low_stock_level, products.cost, products.list_price, products.image_url, 
              (products.stock / products.low_stock_level) AS division 
              FROM products 
              JOIN categories ON products.category_id = categories.id 
              WHERE (products.stock / products.low_stock_level) <= 1 
              AND products.is_active = 1
              ORDER BY division DESC"; // Ordena de mayor a menor en base a la división
              
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getSoldQuantities() {
    $query = "
        SELECT 
            p.id AS product_id,
            COALESCE(SUM(s.quantity), 0) AS sold_quantity -- Cálculo de vendidos
        FROM 
            products p
        LEFT JOIN 
            sale_details s ON p.id = s.product_id -- Relación con las ventas
        GROUP BY 
            p.id
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getTotalStockValue() {
    try {
        $query = "SELECT SUM(stock * cost) AS total_stock_value FROM products WHERE is_active = 1"; // Solo productos activos
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_stock_value'] ?? 0; // Devuelve el valor total o 0 si no hay datos
    } catch (Exception $e) {
        throw new Exception("Error al calcular el valor del stock: " . $e->getMessage());
    }
}





    
    

}
