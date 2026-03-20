<?php
// Ruta: control_stock/backend/controllers/saleController.php 

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../backend/config/database.php';
require_once 'productController.php';  // <-- agregado para ajustar stock con ProductController

class SaleController {
    private $conn;
    private $productController;          // <-- agregado

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->productController = new ProductController();  // <-- agregado
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
    public function getProductSalesByDateRange($startDate, $endDate) {
    $query = "
        SELECT 
            p.name AS product_name,
            sd.price AS price_unit,
            SUM(sd.quantity) AS total_quantity,
            SUM(sd.quantity * sd.price) AS total_amount
        FROM sale_details sd
        INNER JOIN products p ON sd.product_id = p.id
        INNER JOIN sales s ON sd.sale_id = s.id
        WHERE s.sale_date BETWEEN :start_date AND :end_date
        GROUP BY p.id, sd.price
        ORDER BY p.name ASC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getSaleDetails($sale_id) {
        $query = "
            SELECT s.id AS sale_id, s.sale_date, s.total AS sale_total, s.amount_paid, s.client_id,
                   c.name AS client_name, 
                   p.id AS product_id, p.name AS product_name, si.quantity, si.price, si.discount 
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
    
        // Organizar los detalles incluyendo amount_paid y client_id
        $saleDetails = [
            'sale_id'    => $saleData[0]['sale_id'],
            'sale_date'  => $saleData[0]['sale_date'],
            'sale_total' => $saleData[0]['sale_total'],
            'amount_paid'=> $saleData[0]['amount_paid'],
            'client_id'  => $saleData[0]['client_id'],
            'client_name'=> $saleData[0]['client_name'],
            'items'      => []
        ];
    
        foreach ($saleData as $row) {
            $saleDetails['items'][] = [
                'product_id'   => $row['product_id'],
                'product_name' => $row['product_name'],
                'quantity'     => $row['quantity'],
                'price'        => $row['price'],
                'discount'     => $row['discount']
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
                $stmtUpdate->bindParam(':quantity',   $detail['quantity'], PDO::PARAM_INT);
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
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error eliminando la venta y restituyendo el stock: " . $e->getMessage());
            return false;
        }
    }

    // Obtener los años en los que hubo ventas
    public function getYearsWithSales() {
        $query = "SELECT DISTINCT YEAR(sale_date) AS year FROM sales ORDER BY year DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Obtener los meses en los que hubo ventas
    public function getMonthsWithSales() {
        return [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];
    }

    // Obtener ventas filtradas por año y mes
    public function getSalesByYearAndMonth($year, $month = null) {
        if ($month && $month != '') {
            $query = "SELECT DAY(sale_date) AS day, SUM(total) AS total_sales 
                      FROM sales 
                      WHERE YEAR(sale_date) = :year AND MONTH(sale_date) = :month
                      GROUP BY DAY(sale_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year',  $year,  PDO::PARAM_INT);
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
            $key = ($month && $month != '') ? $row['day'] : $row['month'];
            $sales[$key] = $row['total_sales'];
        }

        return $sales;
    }

    // Obtener el total de ventas por año y mes
    public function getTotalSalesByYearAndMonth($year, $month = null) {
        if ($month && $month != '') {
            $query = "SELECT SUM(total) AS total_sales FROM sales WHERE YEAR(sale_date) = ? AND MONTH(sale_date) = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year, $month]);
        } else {
            $query = "SELECT SUM(total) AS total_sales FROM sales WHERE YEAR(sale_date) = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;
    }

    public function updateSale($sale_id, $data) {
        try {
            // Inicia transacción
            $this->conn->beginTransaction();

            // ——— Ajuste de stock antes de actualizar la venta ———
            // 1) Leer cantidades antiguas
            $stmtOld = $this->conn->prepare(
                "SELECT product_id, quantity FROM sale_details WHERE sale_id = :sale_id"
            );
            $stmtOld->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
            $stmtOld->execute();
            $oldDetails = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

            // 2) Mapear viejas y nuevas cantidades
            $oldQty = []; 
            foreach ($oldDetails as $r) {
                $oldQty[$r['product_id']] = (int)$r['quantity'];
            }
            $newQty = []; 
            foreach ($data['items'] as $item) {
                $newQty[$item['product_id']] = (int)$item['quantity'];
            }

            // 3) Calcular delta y ajustar stock
            foreach (array_unique(array_merge(array_keys($oldQty), array_keys($newQty))) as $pid) {
                $delta = ($oldQty[$pid] ?? 0) - ($newQty[$pid] ?? 0);
                if ($delta !== 0) {
                    $this->productController->adjustStock($pid, $delta);
                }
            }
            // ————————————————————————————————————————————

            // Actualiza la cabecera de la venta (por ejemplo, el total)
            $queryHeader = "UPDATE sales SET total = :total WHERE id = :sale_id";
            $stmtHeader = $this->conn->prepare($queryHeader);
            $stmtHeader->bindParam(':total',   $data['total']);
            $stmtHeader->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
            $stmtHeader->execute();

            // Elimina los detalles actuales de la venta
            $queryDelete = "DELETE FROM sale_details WHERE sale_id = :sale_id";
            $stmtDelete  = $this->conn->prepare($queryDelete);
            $stmtDelete->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Inserta los nuevos detalles
            $queryInsert = "INSERT INTO sale_details (sale_id, product_id, quantity, price, discount) VALUES (:sale_id, :product_id, :quantity, :price, :discount)";
            $stmtInsert  = $this->conn->prepare($queryInsert);

            foreach ($data['items'] as $item) {
                $stmtInsert->bindParam(':sale_id',    $sale_id,            PDO::PARAM_INT);
                $stmtInsert->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $stmtInsert->bindParam(':quantity',   $item['quantity'],   PDO::PARAM_INT);
                $stmtInsert->bindParam(':price',      $item['price']);
                $stmtInsert->bindParam(':discount',   $item['discount']);
                $stmtInsert->execute();
            }

            // Confirma la transacción
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error actualizando la venta y ajustando stock: " . $e->getMessage());
            return false;
        }
    }

    public function getMonthlyProfitSummary($year, $month = null) {
        $where = "YEAR(s.sale_date) = :year";
        if ($month && $month != '') {
            $where .= " AND MONTH(s.sale_date) = :month";
        }

        // Ingresos y Costo de lo Vendido
        $query = "
            SELECT 
                SUM(sd.price * sd.quantity) AS total_revenue,
                SUM(p.cost * sd.quantity) AS total_cogs
            FROM sale_details sd
            JOIN sales s ON sd.sale_id = s.id
            JOIN products p ON sd.product_id = p.id
            WHERE $where
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        if ($month && $month != '') {
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        }
        $stmt->execute();
        $salesData = $stmt->fetch(PDO::FETCH_ASSOC);

        $whereCosts = "YEAR(date) = :year";
        if ($month && $month != '') {
            $whereCosts .= " AND MONTH(date) = :month";
        }
        // Gastos Operativos (desde operating_costs)
        $queryCosts = "
            SELECT SUM(amount) AS total_op_costs
            FROM operating_costs
            WHERE $whereCosts
        ";
        $stmtCosts = $this->conn->prepare($queryCosts);
        $stmtCosts->bindParam(':year', $year, PDO::PARAM_INT);
        if ($month && $month != '') {
            $stmtCosts->bindParam(':month', $month, PDO::PARAM_INT);
        }
        $stmtCosts->execute();
        $costsData = $stmtCosts->fetch(PDO::FETCH_ASSOC);

        return [
            'revenue' => $salesData['total_revenue'] ?? 0,
            'cogs' => $salesData['total_cogs'] ?? 0,
            'operating_costs' => $costsData['total_op_costs'] ?? 0
        ];
    }
}
