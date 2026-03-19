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
    <title>Lista de Productos | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.4rem 0.8rem; margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0; border-radius: 0.4rem; padding: 0.2rem;
        }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .badge-stock { padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.75rem; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Lista de Productos</h2>
                    <div class="d-flex gap-2">
                        <button id="downloadPdf" class="modern-btn modern-btn-primary" style="background: #ef4444; width: auto; font-size: 0.875rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/></svg>
                            Imprimir PDF
                        </button>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-info d-flex align-items-center" style="border-radius: 0.75rem; border: none; background: #eff6ff; color: #1e40af;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <strong>Valor total del Stock (Costo):</strong>&nbsp; $<?php echo number_format($totalStockValue, 2); ?>
                </div>

                <div class="row items-center mb-4 mt-4">
                    <div class="col-md-4">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">Filtrar por Categoría</label>
                        <select id="categoryFilter" class="modern-input">
                            <option value="">Todas las Categorías</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="productTable" class="table">
                        <thead>
                            <tr>
                                <th>Cod.</th>
                                <th>Nombre</th>
                                <th>Catg.</th>
                                <th>Stock</th>
                                <th>Costo</th>
                                <th>Precio</th>
                                <th>Vendidos</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr data-category="<?php echo $product['category_name']; ?>">
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $product['cod']; ?></td>
                                    <td style="font-weight: 600;"><?php echo $product['name']; ?></td>
                                    <td><span class="badge" style="background: #f1f5f9; color: #475569;"><?php echo $product['category_name']; ?></span></td>
                                    <td>
                                        <?php 
                                            $stock_class = $product['stock'] <= $product['low_stock_level'] ? 'bg-danger text-white' : 'bg-success text-white';
                                        ?>
                                        <span class="badge-stock <?php echo $stock_class; ?>"><?php echo $product['stock']; ?></span>
                                    </td>
                                    <td>$<?php echo number_format($product['cost'], 2); ?></td>
                                    <td style="font-weight: 700; color: var(--primary);">$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $soldMap[$product['id']] ?? 0; ?></td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a href="modifyProduct.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></a>
                                            <button class="btn btn-sm btn-outline-success addStockBtn" data-id="<?php echo $product['id']; ?>" title="+ Stock"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg></button>
                                            <button class="btn btn-sm btn-outline-warning removeStockBtn" data-id="<?php echo $product['id']; ?>" title="- Stock"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg></button>
                                            <a href="../../backend/controllers/deleteProductHandler.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger deleteBtn" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para agregar y quitar stock -->
    <!-- Modales Modernizados -->
    <style>
        .modern-modal .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: var(--shadow-strong);
            padding: 1rem;
        }
        .modern-modal .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modern-modal .modal-title {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1.5rem;
        }
        .modern-modal .modal-footer {
            border-top: none;
            padding-top: 0;
            gap: 0.75rem;
        }
    </style>

    <!-- Modal para Agregar Stock -->
    <div class="modal fade modern-modal" id="addStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/addStockHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="addStockProductId">
                        <div class="form-group">
                            <label class="modern-label">Cantidad a Agregar</label>
                            <input type="number" class="modern-input" id="addStockAmount" name="amount" placeholder="Ej: 10" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn" style="background: #f1f5f9; color: #475569;" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary">Actualizar Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Quitar Stock -->
    <div class="modal fade modern-modal" id="removeStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quitar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/removeStockHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="removeStockProductId">
                        <div class="form-group">
                            <label class="modern-label">Cantidad a Quitar</label>
                            <input type="number" class="modern-input" id="removeStockAmount" name="amount" placeholder="Ej: 5" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn" style="background: #f1f5f9; color: #475569;" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="background: #ef4444;">Confirmar Retiro</button>
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

