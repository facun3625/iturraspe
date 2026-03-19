<?php
// Ruta: control_stock/frontend/pages/productList.php

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
    $products = $productController->getProductsWithCalculatedPrice(); // Cambiamos a la nueva función
    $categories = $productController->getAllCategories(); // Obtener las categorías
} catch (Exception $e) {
    $products = [];
    $categories = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}
try {
    $products = $productController->getProductsWithCalculatedPrice();
    $soldQuantities = $productController->getSoldQuantities(); // Nueva función
} catch (Exception $e) {
    $products = [];
    $soldQuantities = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}
try {
    $totalStockValue = $productController->getTotalStockValue();
} catch (Exception $e) {
    $totalStockValue = 0;
    $error = "Error al calcular el valor total del stock: " . $e->getMessage();
}



// Crear un mapa de productos vendidos para fácil acceso
$soldMap = [];
foreach ($soldQuantities as $sold) {
    $soldMap[$sold['product_id']] = $sold['sold_quantity'];
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center pb-4">Lista de Productos</h4>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <!-- Botón para descargar PDF -->
<div class="text-right">
    <button id="downloadPdf" class="btn btn-danger btn-sm">imprimir en PDF</button>
</div><br>
<div class="alert alert-info text-left">
    <strong>Valor total del Stock segun Costo:</strong> $<?php echo number_format($totalStockValue, 2); ?>
</div>


        <!-- Selector de categoría -->
        <div class="form-group mb-5">
            <label for="categoryFilter">Filtrar por Categoría:</label>
            <select id="categoryFilter" class="form-control">
                <option value="">Todas las Categorías</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table id="productTable" class="table table-striped">
        <thead>
    <tr>
        <th>ID</th>
        <th style="max-width: 200px;">Nombre</th>
        <th>Catg.</th>
        <th>Stock</th>
        <th>Alerta</th>
        <th>Costo</th>
        <th>Precio</th>
        <th>Vendidos</th> <!-- Nueva columna -->
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($products as $product): ?>
        <tr data-category="<?php echo $product['category_name']; ?>">
            <td class="text-center"><?php echo $product['cod']; ?></td>
            <td class="text-center" style="max-width: 200px;"><?php echo $product['name']; ?></td>
            <td class="text-center"><?php echo $product['category_name']; ?></td>
            <td class="text-center"><?php echo $product['stock']; ?></td>
            <td class="text-center"><?php echo $product['low_stock_level']; ?></td>
            <td class="text-center">$<?php echo number_format($product['cost'], 2); ?></td>
            <td class="text-center">$<?php echo number_format($product['price'], 2); ?></td>
            <td class="text-center"><?php echo $soldMap[$product['id']] ?? 0; ?></td> <!-- Mostrar Vendidos -->
            <td class="text-center" style="white-space: nowrap">
                <a href="modifyProduct.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                <a href="../../backend/controllers/deleteProductHandler.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm deleteBtn">Eliminar</a>
                <button class="btn btn-success btn-sm addStockBtn" data-id="<?php echo $product['id']; ?>" data-stock="<?php echo $product['stock']; ?>">+ Stock</button>
                <button class="btn btn-secondary btn-sm removeStockBtn" data-id="<?php echo $product['id']; ?>" data-stock="<?php echo $product['stock']; ?>">- Stock</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>

    <!-- Modales para agregar y quitar stock -->
     <!-- Modal para Agregar Stock -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Agregar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/addStockHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="addStockProductId">
                        <div class="form-group">
                            <label for="addStockAmount">Cantidad a Agregar</label>
                            <input type="number" class="form-control" id="addStockAmount" name="amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Quitar Stock -->
    <div class="modal fade" id="removeStockModal" tabindex="-1" aria-labelledby="removeStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeStockModalLabel">Quitar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/removeStockHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="removeStockProductId">
                        <div class="form-group">
                            <label for="removeStockAmount">Cantidad a Quitar</label>
                            <input type="number" class="form-control" id="removeStockAmount" name="amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Quitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- (Mantenemos los mismos modales de antes para agregar y quitar stock) -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#productTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                }
            });

            // Filtrar productos por categoría
            $('#categoryFilter').on('change', function() {
                const category = $(this).val();
                if (category) {
                    table.column(2).search('^' + category + '$', true, false).draw(); // Filtra la columna de categoría
                } else {
                    table.column(2).search('').draw(); // Muestra todas las categorías si no hay filtro
                }
            });

            // Agregar Stock
            $('.addStockBtn').on('click', function() {
                $('#addStockProductId').val($(this).data('id'));
                $('#addStockAmount').val(''); // Limpiar campo de cantidad
                $('#addStockModal').modal('show');
            });

            // Quitar Stock
            $('.removeStockBtn').on('click', function() {
                $('#removeStockProductId').val($(this).data('id'));
                $('#removeStockAmount').val(''); // Limpiar campo de cantidad
                $('#removeStockModal').modal('show');
            });
        });
    </script>
    <script>
    document.getElementById('downloadPdf').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título del documento
    doc.text('Lista de Precios', 14, 10);

    // Stock valorizado ya calculado en PHP
    const totalStockValue = "<?php echo number_format($totalStockValue, 2); ?>";

    // Mostrar el stock valorizado debajo del título
    doc.text(`Stock Valorizado: $${totalStockValue}`, 14, 20);

    // Generar la tabla de precios
    doc.autoTable({
        startY: 30, // Para que la tabla no se superponga con el texto
        head: [['ID', 'Nombre', 'Categoría', 'Stock', 'Costo', 'Precio']],
        body: <?php echo json_encode(array_map(function($product) {
            return [
                $product['cod'],
                $product['name'],
                $product['category_name'],
                $product['stock'],
                '$' . number_format($product['cost'], 2),
                '$' . number_format($product['price'], 2)
            ];
        }, $products)); ?>
    });

    // Descargar el archivo
    doc.save('lista_de_precios.pdf');
});

</script>
</body>
</html>
<?php
// Ruta: control_stock/frontend/pages/productList.php

require_once '../../backend/controllers/productController.php';
$productController = new ProductController();

try {
    $products = $productController->getProductsWithCategory();
} catch (Exception $e) {
    $products = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}
?>

