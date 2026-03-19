<?php
// Archivo: control_stock/backend/updatePrices.php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
class Database {
    private $host = "localhost";
    private $db_name = "distji_s";
    private $username = "distji_s"; // Cambia este dato si usas otro usuario
    private $password = "J*OqzaQ4fW";     // Cambia este dato si tienes contraseña configurada
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Controlador de productos
class ProductController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findProductByCode($cod) {
        $selectQuery = "SELECT * FROM products WHERE cod = :cod";
        $selectStmt = $this->conn->prepare($selectQuery);
        $selectStmt->bindParam(':cod', $cod);
        $selectStmt->execute();

        return $selectStmt->rowCount() > 0;
    }

    public function updatePriceByCode($cod, $new_price_list) {
        $query = "UPDATE products SET list_price = :list_price WHERE cod = :cod";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':list_price', $new_price_list);
        $stmt->bindParam(':cod', $cod);

        return $stmt->execute();
    }
}

// Manejo de la subida de archivos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['price_updates'])) {
    $file = $_FILES['price_updates'];

    // Validar si el archivo se subió sin errores
    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($fileType == 'csv') {
            $handle = fopen($file['tmp_name'], 'r');

            $productController = new ProductController();
            $actualizaciones = 0; // Contador de actualizaciones exitosas

            // Leer cada línea del archivo CSV
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) < 2) {
                    continue;
                }

                $cod = trim($data[0]);
                $new_price_list = floatval(trim($data[1]));

                // Verificar si el producto existe y actualizar el precio
                if ($productController->findProductByCode($cod)) {
                    if ($productController->updatePriceByCode($cod, $new_price_list)) {
                        $actualizaciones++;
                    }
                }
            }
            fclose($handle);

            // Redirigir después de la carga
            header("Location: priceUpdateConfirmation.php?success=1&updates=$actualizaciones");
            exit();
        } else {
            die("Formato de archivo no soportado.");
        }
    } else {
        die("Error al subir el archivo. Código de error: " . $file['error']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Precios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once '../components/navbar.php'; ?>
    <div class="rounded-container-form">
        <h4 class="text-center mb-4">Subir Archivo de Precios</h4>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="price_updates">Archivo CSV:</label>
                <input type="file" class="form-control-file" id="price_updates" name="price_updates" accept=".csv" required>
            </div>

            <button type="submit" class="btn btn-primary">Subir y Actualizar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
