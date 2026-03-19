<?php
// Ruta: control_stock/frontend/pages/priceList.php

require_once 'backend/controllers/productController.php';
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
        /* Reducir la tipografía de la tabla para dispositivos móviles */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <h4 class="text-center pb-4">Lista de Precios</h4>
        
        <!-- Botones de exportación -->
        <div class="mb-3">
            <button id="exportCsv" class="btn btn-success">Exportar a CSV</button>
            <button id="exportExcel" class="btn btn-primary">Exportar a Excel</button>
        </div>

        <!-- Agrega un input de búsqueda -->
        <div class="form-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar producto...">
        </div>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <!-- Se asigna el id "priceTable" a la tabla -->
            <table class="table table-bordered" id="priceTable">
                <thead class="thead-dark">
                    <tr>
                        <th>id</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td style="white-space: normal; font-size: 13px;"><?php echo htmlspecialchars($product['id']); ?></td>
                            <td style="white-space: normal;font-size: 13px;"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td style="white-space: normal; font-size: 13px;"><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td style="white-space: normal; font-size: 13px;">$<?php echo number_format($product['price'], 2); ?></td>
                            <td >
                                <?php if (!empty($product['image_url'])): ?>
                                    <button class="btn btn-info btn-sm" onclick="showImageModal('<?php echo $product['image_url']; ?>')">Imagen</button>
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para la imagen -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" class="img-fluid" alt="Imagen del producto">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- SheetJS (xlsx) vía CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    // Función para mostrar la imagen en el modal
    function showImageModal(imageUrl) {
        var fullPath = 'frontend/' + imageUrl;
        $('#modalImage').attr('src', fullPath);
        $('#imageModal').modal('show');
    }

    // Buscador en tiempo real: filtra las filas de la tabla conforme se escribe en el input
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#priceTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    // Función para exportar la tabla a CSV
    function exportTableToCSV(filename) {
        var csv = [];
        var rows = document.querySelectorAll("#priceTable tr");
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            for (var j = 0; j < cols.length; j++) {
                // Limpiar saltos de línea y comillas
                var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }

        // Crear archivo CSV
        var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        var downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    // Función para exportar la tabla a Excel usando SheetJS
    function exportTableToExcelJS(filename = 'lista_de_precios.xlsx'){
        // Obtiene la tabla
        var table = document.getElementById("priceTable");
        // Convierte la tabla HTML a una hoja de trabajo
        var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
        // Genera el archivo Excel y fuerza la descarga
        XLSX.writeFile(workbook, filename);
    }

    // Eventos para los botones de exportación
    document.getElementById("exportCsv").addEventListener("click", function () {
        exportTableToCSV('lista_de_precios.csv');
    });
    document.getElementById("exportExcel").addEventListener("click", function () {
        exportTableToExcelJS();
    });
    </script>
</body>
</html>
