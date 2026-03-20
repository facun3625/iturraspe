<?php
// Archivo: control_stock/frontend/pages/uploadPrices.php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';
$authController = new AuthController();
if (!$authController->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

require_once '../../backend/config/database.php';

// Controlador de productos interno para este proceso masivo
class BulkProductController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findProductByCode($cod) {
        $selectQuery = "SELECT id FROM products WHERE cod = :cod";
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
$message = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['price_updates'])) {
    $file = $_FILES['price_updates'];

    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($fileType == 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            $productController = new BulkProductController();
            $actualizaciones = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) < 2) continue;
                $cod = trim($data[0]);
                $new_price_list = floatval(trim($data[1]));
                if ($productController->findProductByCode($cod)) {
                    if ($productController->updatePriceByCode($cod, $new_price_list)) {
                        $actualizaciones++;
                    }
                }
            }
            fclose($handle);
            $message = ["type" => "success", "text" => "Se actualizaron $actualizaciones productos correctamente."];
        } else {
            $message = ["type" => "error", "text" => "Formato de archivo no soportado. Debe ser CSV."];
        }
    } else {
        $message = ["type" => "error", "text" => "Error al subir el archivo."];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Precios Masivo | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .form-card { background: white; border-radius: 1.5rem; padding: 2.5rem; border: none; box-shadow: var(--shadow-soft); max-width: 700px; margin: 0 auto; }
        .upload-zone {
            border: 2px dashed #e2e8f0; border-radius: 1rem; padding: 3rem; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s;
        }
        .upload-zone:hover { border-color: var(--primary); background: #eff6ff; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="form-card">
                <div class="mb-5 text-center">
                    <h2 style="font-weight: 700; color: var(--text-main);">Actualización de Precios</h2>
                    <p style="color: var(--text-muted);">Sube un archivo CSV para actualizar los precios de lista masivamente.</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message['type'] == 'success' ? 'success' : 'danger'; ?>" style="border-radius: 0.75rem; border: none;">
                        <?php echo $message['text']; ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info" style="border-radius: 0.75rem; border: none; background: #eff6ff; color: #1e40af; font-size: 0.875rem; margin-bottom: 2rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <strong>Formato requerido:</strong> Archivo CSV con dos columnas: <code>CODIGO, PRECIO_LISTA</code>.
                </div>

                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-4">
                        <div class="upload-zone" onclick="document.getElementById('price_updates').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                            <h5 style="color: var(--text-main); font-weight: 600;">Subir Archivo CSV</h5>
                            <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Haz clic para seleccionar el archivo de tu equipo</p>
                            <input type="file" id="price_updates" name="price_updates" accept=".csv" required style="display: none;">
                        </div>
                    </div>

                    <button type="submit" class="modern-btn modern-btn-primary">
                        Comenzar Actualización
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
