<?php
// Ruta: control_stock/frontend/pages/productListLowStock.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';

$authController = new AuthController();

// Verifica si el usuario está autenticado
if (!$authController->isLoggedIn()) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}

require_once '../../backend/controllers/productController.php';
$productController = new ProductController();

try {
    $products = $productController->getLowStockProducts(); // Llamada al nuevo método
} catch (Exception $e) {
    $products = [];
    $error = "Error al cargar los productos en alerta de stock: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos en Alerta de Stock</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Clase para mostrar el texto en rojo y negrita */
        .text-red-bold {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include_once '../components/navbar.php'; ?>

    <!-- Título y Botón para Imprimir en PDF -->
    <div class="rounded-container-table">
        <h4 class="text-center pb-4">Productos en Alerta de Stock</h4>
        
        <div class="text-right mb-3">
            <button id="printPdfBtn" class="btn btn-danger">Imprimir en PDF</button>
        </div>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Tabla de productos en alerta -->
        <table id="productTable" class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Categoría</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Alerta</th>
                    <th class="text-center">División</th> <!-- Columna de división, será oculta -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php $highlightClass = ($product['division'] < 1) ? 'text-red-bold' : ''; ?>
                    <tr>
                        <td class="text-center"><?php echo $product['cod']; ?></td>
                        <td class="text-center <?php echo $highlightClass; ?>"><?php echo $product['name']; ?></td>
                        <td class="text-center"><?php echo $product['category_name']; ?></td>
                        <td class="text-center <?php echo $highlightClass; ?>"><?php echo $product['stock']; ?></td>
                        <td class="text-center <?php echo $highlightClass; ?>"><?php echo $product['low_stock_level']; ?></td>
                        <td class="text-center"><?php echo number_format($product['division'], 2); ?></td> <!-- Columna de división, se oculta en DataTables -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#productTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                },
                "order": [[5, 'asc']], // Ordenar por la columna de división (índice 5) de menor a mayor
                "columnDefs": [
                    { "targets": 5, "visible": false } // Ocultar la columna de división
                ]
            });

            // Función para generar PDF
            $('#printPdfBtn').on('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                // Título del PDF
                doc.text('Productos en Alerta de Stock', 14, 10);

                // Generar tabla en PDF usando autoTable
                const tableColumns = ['ID', 'Nombre', 'Categoría', 'Stock', 'Alerta']; // Encabezados
                const tableRows = <?php echo json_encode(array_map(function($product) {
                    return [
                        $product['cod'],
                        $product['name'],
                        $product['category_name'],
                        $product['stock'],
                        $product['low_stock_level']
                    ];
                }, $products)); ?>;

                doc.autoTable({
                    head: [tableColumns],
                    body: tableRows,
                    startY: 20 // Espacio desde el inicio del documento
                });

                // Guardar el PDF
                doc.save('productos_en_alerta_stock.pdf');
            });
        });
    </script>
</body>
</html>
