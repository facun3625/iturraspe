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
            <!-- <button id="downloadPdf" class="btn btn-primary">Descargar PDF</button> -->
            Link de descarga para enviar: <br><br><div id="linkToCopy">https://distribuidoraji.com.ar/lista.php</div><br>
    <button class="btn btn-secondary" onclick="copyLink()">Copiar Enlace</button>
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
    doc.setFontSize(18);
    doc.text('Lista de Precios', 14, 15);

    // Encabezado de la tabla
    const tableHead = [['ID', 'Nombre', 'Categoría', 'Precio', 'Detalle']];

    // Datos de la tabla
    const tableData = <?php echo json_encode(array_map(function ($product) {
        return [
            $product['id'], // Cambiado a ID
            $product['name'], // Nombre
            $product['category_name'], // Categoría
            '$' . number_format($product['price'], 2), // Precio
            "https://distribuidoraji.com.ar/frontend/pages/productDetail.php?id={$product['id']}" // Enlace al detalle
        ];
    }, $products)); ?>;

    // Crear la tabla con enlaces
    doc.autoTable({
        startY: 25,
        head: tableHead,
        body: tableData.map((row, index) => [
            row[0], // ID
            row[1], // Nombre
            row[2], // Categoría
            row[3], // Precio
        ]),
        headStyles: {
            fillColor: [41, 128, 185], // Azul oscuro
            textColor: [255, 255, 255], // Blanco
            halign: 'center', // Centrado
            fontSize: 12,
        },
        bodyStyles: {
            textColor: [0, 0, 0], // Negro
            halign: 'center', // Centrado
            lineColor: [200, 200, 200], // Líneas suaves
            lineWidth: 0.1,
        },
        styles: {
            fontSize: 10, // Tamaño de fuente
            cellPadding: 5, // Espaciado interno
        },
        margin: { top: 20 }, // Margen superior
        didDrawCell: function (data) {
            if (data.column.index === 4 && data.cell.section === 'body') {
                // Ajustar posición del texto "Ver ficha"
                const link = tableData[data.row.index][4];
                doc.setTextColor(0, 0, 255); // Azul para el texto
                doc.textWithLink('Ver Imagen', data.cell.x + data.cell.width / 2 - 10, data.cell.y + data.cell.height / 2 + 2, { url: link });
            }
        },
    });

    // Guardar el PDF
    doc.save('lista_de_precios.pdf');
});
</script>
<script>
function copyLink() {
    // Obtener el elemento que contiene el texto
    var linkToCopy = document.getElementById("linkToCopy").innerText;
    var tempInput = document.createElement("input"); // Crear un input temporal
    document.body.appendChild(tempInput); // Añadir el input al body
    tempInput.value = linkToCopy; // Asignar el valor del enlace al input
    tempInput.select(); // Seleccionar el contenido del input
    document.execCommand("copy"); // Copiar el contenido seleccionado
    document.body.removeChild(tempInput); // Eliminar el input temporal
    alert("Enlace copiado al portapapeles"); // Alerta de confirmación
}
</script>



</body>
</html>
