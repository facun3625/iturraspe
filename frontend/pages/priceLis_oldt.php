<?php
// Ruta: control_stock/frontend/pages/priceList.php

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
    $products = $productController->getProductsWithCalculatedPrice(); // Usamos la función para obtener productos con el precio calculado

    // Ordenar los productos alfabéticamente por 'name'
    usort($products, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
} catch (Exception $e) {
    $products = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Precios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Asegurar que las imágenes estén centradas verticalmente */
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center pb-4">Lista de Precios</h4>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Botón para generar PDF -->
        <div class="text-center mb-3 ">
            <button id="downloadPdf" class="btn btn-primary">Descargar PDF</button>
        </div>

        <div class="table-responsive">
            <table id="priceTable" class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['cod']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category_name']; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <div class="image-container mb-3">
                                    <?php if (!empty($product['image_url'])): ?>
                                        <img src="../<?php echo $product['image_url']; ?>" alt="Imagen del producto" class="img-thumbnail" style="max-width: 300px; height: auto;">
                                    <?php else: ?>
                                        <p>No hay imagen para este producto.</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
    document.getElementById('downloadPdf').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Título del documento
        doc.text('Lista de Precios', 14, 10);

        // Agregar el enlace al PDF
        doc.setTextColor(0, 0, 255); // Color azul para el enlace
        doc.textWithLink('Para ver la lista de precios Online con imágenes haz clic aquí', 14, 20, {
            url: 'https://distribuidoraji.com.ar/frontend/pages/listaDePrecios.php'
        });

        // Tabla de precios
        doc.autoTable({
            startY: 30, // Para que la tabla no se superponga con el texto
            head: [['ID', 'Nombre', 'Categoría', 'Precio']],
            body: <?php echo json_encode(array_map(function($product) {
                return [
                    $product['cod'],
                    $product['name'],
                    $product['category_name'],
                    '$' . number_format($product['price'], 2)
                ];
            }, $products)); ?>
        });

        doc.save('lista_de_precios.pdf');
    });
</script>

</body>
</html>
