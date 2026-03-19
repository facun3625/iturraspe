<?php
// Ruta: control_stock/backend/controllers/saleController.php 

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../backend/config/database.php';

class SaleController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener las ventas agrupadas
    public function getSalesGrouped() {
        $query = "
            SELECT 
                s.id as sale_id, 
                s.sale_date as sale_date, 
                s.total as sale_total, 
                c.name as client_name
            FROM 
                sales s
            JOIN 
                clients c ON s.client_id = c.id
            ORDER BY 
                s.sale_date DESC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSaleDetails($sale_id) {
        $query = "
            SELECT s.id AS sale_id, s.sale_date, s.total AS sale_total, s.amount_paid,  -- Incluye amount_paid aquí
                   c.name AS client_name, 
                   p.id AS product_id, p.name AS product_name, si.quantity, si.price 
            FROM sales s
            JOIN clients c ON s.client_id = c.id
            JOIN sale_details si ON s.id = si.sale_id
            JOIN products p ON si.product_id = p.id
            WHERE s.id = :sale_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->execute();
        $saleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$saleData) {
            return null;
        }
    
        // Organizar los detalles incluyendo amount_paid
        $saleDetails = [
            'sale_id' => $saleData[0]['sale_id'],
            'sale_date' => $saleData[0]['sale_date'],
            'sale_total' => $saleData[0]['sale_total'],
            'amount_paid' => $saleData[0]['amount_paid'],  // Incluye el total pagado aquí
            'client_name' => $saleData[0]['client_name'],
            'items' => []
        ];
    
        foreach ($saleData as $row) {
            $saleDetails['items'][] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }
    
        return $saleDetails;
    }
    
    public function getSalesByClientId($clientId) {
        $query = "SELECT s.id AS sale_id, s.sale_date, s.total AS sale_total, s.amount_paid, 
                         c.name AS client_name 
                  FROM sales s
                  JOIN clients c ON s.client_id = c.id
                  WHERE s.client_id = :client_id
                  ORDER BY s.sale_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSale($saleId) {
    try {
        // Inicia una transacción para asegurar consistencia en la base de datos
        $this->conn->beginTransaction();

        // Primero, recuperar los detalles de la venta para restituir el stock
        $queryDetails = "SELECT product_id, quantity FROM sale_details WHERE sale_id = :sale_id";
        $stmtDetails = $this->conn->prepare($queryDetails);
        $stmtDetails->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmtDetails->execute();
        $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        // Actualiza el stock para cada producto
        foreach ($details as $detail) {
            $updateStock = "UPDATE products SET stock = stock + :quantity WHERE id = :product_id";
            $stmtUpdate = $this->conn->prepare($updateStock);
            $stmtUpdate->bindParam(':quantity', $detail['quantity'], PDO::PARAM_INT);
            $stmtUpdate->bindParam(':product_id', $detail['product_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();
        }

        // Ahora, elimina los detalles de la venta
        $queryDeleteDetails = "DELETE FROM sale_details WHERE sale_id = :sale_id";
        $stmtDeleteDetails = $this->conn->prepare($queryDeleteDetails);
        $stmtDeleteDetails->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmtDeleteDetails->execute();

        // Finalmente, elimina la venta
        $querySale = "DELETE FROM sales WHERE id = :sale_id";
        $stmtSale = $this->conn->prepare($querySale);
        $stmtSale->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmtSale->execute();

        // Confirma la transacción
        $this->conn->commit();
        return true; // Venta eliminada exitosamente
    } catch (Exception $e) {
        // En caso de error, revierte la transacción
        $this->conn->rollBack();
        error_log("Error eliminando la venta y restituyendo el stock: " . $e->getMessage());
        return false; // Indica que hubo un error
    }
}


    // Obtener los años en los que hubo ventas
    public function getYearsWithSales() {
        $query = "SELECT DISTINCT YEAR(sale_date) AS year FROM sales ORDER BY year DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve un array con los años
    }

    // Obtener los meses en los que hubo ventas
    public function getMonthsWithSales() {
        return [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
    }

    // Obtener ventas filtradas por año y mes
    public function getSalesByYearAndMonth($year, $month = null) {
        if ($month) {
            $query = "SELECT DAY(sale_date) AS day, SUM(total) AS total_sales 
                      FROM sales 
                      WHERE YEAR(sale_date) = :year AND MONTH(sale_date) = :month
                      GROUP BY DAY(sale_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        } else {
            $query = "SELECT MONTH(sale_date) AS month, SUM(total) AS total_sales 
                      FROM sales 
                      WHERE YEAR(sale_date) = :year
                      GROUP BY MONTH(sale_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sales = [];
        foreach ($results as $row) {
            $key = $month ? $row['day'] : $row['month'];
            $sales[$key] = $row['total_sales'];
        }

        return $sales;
    }

    // Obtener el total de ventas por año y mes
    public function getTotalSalesByYearAndMonth($year, $month = null) {
        if ($month) {
            $query = "SELECT SUM(total) AS total_sales FROM sales WHERE YEAR(sale_date) = ? AND MONTH(sale_date) = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year, $month]);
        } else {
            $query = "SELECT SUM(total) AS total_sales FROM sales WHERE YEAR(sale_date) = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0; // Devuelve el total o 0 si no hay ventas
    }
    public function updateSale($sale_id, $data) {
        try {
            // Inicia transacción
            $this->conn->beginTransaction();

            // Actualiza la cabecera de la venta (por ejemplo, el total)
            $queryHeader = "UPDATE sales SET total = :total WHERE id = :sale_id";
            $stmtHeader = $this->conn->prepare($queryHeader);
            $stmtHeader->bindParam(':total', $data['total']);
            $stmtHeader->bindParam(':sale_id', $sale_id);
            $stmtHeader->execute();

            // Elimina los detalles actuales de la venta
            $queryDelete = "DELETE FROM sale_details WHERE sale_id = :sale_id";
            $stmtDelete = $this->conn->prepare($queryDelete);
            $stmtDelete->bindParam(':sale_id', $sale_id);
            $stmtDelete->execute();

            // Inserta los nuevos detalles
            $queryInsert = "INSERT INTO sale_details (sale_id, product_id, quantity, price) VALUES (:sale_id, :product_id, :quantity, :price)";
            $stmtInsert = $this->conn->prepare($queryInsert);

            foreach ($data['items'] as $item) {
                // Es posible que debas ajustar los nombres de las claves según la estructura que uses (por ejemplo, product_id o id)
                $stmtInsert->bindParam(':sale_id', $sale_id);
                $stmtInsert->bindParam(':product_id', $item['product_id']);
                $stmtInsert->bindParam(':quantity', $item['quantity']);
                $stmtInsert->bindParam(':price', $item['price']);
                $stmtInsert->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error actualizando la venta: " . $e->getMessage());
            return false;
        }
    }

}
?>
