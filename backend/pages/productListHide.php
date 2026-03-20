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
    $products = $productController->getProductsWithCalculatedPriceHide(); // Cambiamos a la nueva función
    $categories = $productController->getAllCategories(); // Obtener las categorías
} catch (Exception $e) {
    $products = [];
    $categories = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Eliminados | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; transition: transform 0.2s; }
        table.dataTable tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .badge-category { background: #f1f5f9; color: #475569; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 600; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: none; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Papelera de Productos</h2>
                        <p style="color: var(--text-muted); margin: 0;">Listado de productos desactivados que pueden ser restaurados.</p>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="modern-label" style="font-size: 0.75rem;">Filtrar por Categoría</label>
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
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Costo</th>
                                <th>Precio</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr data-category="<?php echo $product['category_name']; ?>">
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $product['cod']; ?></td>
                                    <td style="font-weight: 700; color: var(--text-main);"><?php echo $product['name']; ?></td>
                                    <td><span class="badge-category"><?php echo $product['category_name']; ?></span></td>
                                    <td style="font-weight: 600;"><?php echo $product['stock']; ?></td>
                                    <td style="color: #64748b;">$<?php echo number_format($product['cost'], 2); ?></td>
                                    <td style="font-weight: 700; color: var(--primary);">$<?php echo number_format($product['price'], 2); ?></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="modifyProduct.php?id=<?php echo $product['id']; ?>" class="action-btn" style="background: #fef3c7; color: #92400e;" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </a>
                                            <a href="../../backend/controllers/activeProductHandler.php?id=<?php echo $product['id']; ?>" class="action-btn" style="background: #dcfce7; color: #166534;" title="Activar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                                            </a>
                                            <button class="action-btn addStockBtn" data-id="<?php echo $product['id']; ?>" style="background: #eff6ff; color: #1e40af;" title="Agregar Stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                            </button>
                                            <button class="action-btn removeStockBtn" data-id="<?php echo $product['id']; ?>" style="background: #f1f5f9; color: #475569;" title="Quitar Stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                            </button>
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

    <!-- Modales Modernos -->
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--text-main);">Agregar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="../../backend/controllers/addStockHandler.php" method="post">
                    <div class="modal-body py-4">
                        <input type="hidden" name="id" id="addStockProductId">
                        <div class="form-group mb-0">
                            <label class="modern-label">Cantidad a Incrementar</label>
                            <input type="number" class="modern-input" name="amount" placeholder="Ej: 10" required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; padding-left: 2rem; padding-right: 2rem;">Confirmar Ingreso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeStockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--text-main);">Quitar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="../../backend/controllers/removeStockHandler.php" method="post">
                    <div class="modal-body py-4">
                        <input type="hidden" name="id" id="removeStockProductId">
                        <div class="form-group mb-0">
                            <label class="modern-label">Cantidad a Extraer</label>
                            <input type="number" class="modern-input" name="amount" placeholder="Ej: 5" required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; padding-left: 2rem; padding-right: 2rem;">Confirmar Egreso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#productTable').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                "dom": '<"d-flex justify-content-between align-items-center mb-4"lf>rtip'
            });

            $('#categoryFilter').on('change', function() {
                const category = $(this).val();
                table.column(2).search(category ? '^' + category + '$' : '', true, false).draw();
            });

            $('.addStockBtn').on('click', function() {
                $('#addStockProductId').val($(this).data('id'));
                $('#addStockModal').modal('show');
            });

            $('.removeStockBtn').on('click', function() {
                $('#removeStockProductId').val($(this).data('id'));
                $('#removeStockModal').modal('show');
            });
        });
    </script>
</body>

