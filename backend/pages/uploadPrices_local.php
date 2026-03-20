<?php
// Archivo: control_stock/backend/updatePrices.php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
class Database {
    private $host = "localhost";
    private $db_name = "control_stock";
    private $username = "root"; // Cambia este dato si usas otro usuario
    private $password = "";     // Cambia este dato si tienes contraseña configurada
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

    public function updatePriceByCode($cod, $new_price_list) {
        $query = "UPDATE products SET list_price = :list_price WHERE cod = :cod";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':list_price', $new_price_list);
        $stmt->bindParam(':cod', $cod);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            echo "Error al actualizar: " . $errorInfo[2]; // Muestra el error
            return false;
        }
        return true;
    }
}

// Manejo de la subida de archivos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['price_updates'])) {
    $file = $_FILES['price_updates'];

    // Validar si el archivo se subió sin errores
    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($fileType == 'csv') {
            // Manejar archivos CSV
            $handle = fopen($file['tmp_name'], 'r');

            // Lee la cabecera del archivo CSV
            $header = fgetcsv($handle, 1000, ',');
            if ($header === false) {
                die("Error al leer la cabecera del archivo CSV.");
            }

            // Asegúrate de que las columnas están en el orden correcto
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Suponiendo que el archivo tiene 'cod' en la primera columna y 'list_price' en la segunda
                if (count($data) < 2) {
                    echo "Formato de fila inválido: " . implode(",", $data) . "<br>";
                    continue; // Saltar a la siguiente fila
                }
                $cod = $data[0];
                $new_price_list = $data[1];

                // Llama a la función para actualizar el precio en la base de datos
                $productController = new ProductController();
                $result = $productController->updatePriceByCode($cod, $new_price_list);

                if (!$result) {
                    // Manejar el error si no se actualiza el precio
                    echo "Error al actualizar el producto con código: $cod<br>";
                }
            }
            fclose($handle);
        } else {
            die("Formato de archivo no soportado.");
        }

        // Redirigir después de la carga
        header("Location: priceUpdateConfirmation.php?success=1");
        exit();
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
