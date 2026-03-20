<?php
// Ruta: control_stock/frontend/pages/clientList.php

// Mostrar errores en la pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

require_once '../../backend/controllers/clientController.php';
$clientController = new ClientController();
$clients = $clientController->getClientsWithDebtOnly(); // Este método incluirá el cálculo de deuda

// Calcular el total de deuda
$totalDebt = array_reduce($clients, function ($sum, $client) {
    return $sum + $client['debt'];
}, 0);

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='alert alert-success text-center'>Cliente añadido correctamente.</div>";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes con Deuda | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        .debt-banner { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; border: 1px solid #fecaca; }
        .debt-amount { font-size: 2rem; font-weight: 800; color: #991b1b; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: none; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="debt-banner">
                <div>
                    <h5 style="color: #991b1b; font-weight: 700; margin-bottom: 0.25rem;">Deuda Total Acumulada</h5>
                    <p style="color: #b91c1c; margin: 0; font-size: 0.875rem;">Suma de todos los saldos pendientes de clientes.</p>
                </div>
                <div class="debt-amount">$<?php echo number_format($totalDebt, 2); ?></div>
            </div>

            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Clientes con Saldo Pendiente</h2>
                        <p style="color: var(--text-muted); margin: 0;">Gestioná los pagos y cuentas corrientes.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="printPdfBtn" class="modern-btn" style="width: auto; background: #fee2e2; color: #991b1b; border: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Exportar PDF
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="clientTable" class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Cliente</th>
                                <th class="text-center">Compras</th>
                                <th>Deuda</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td style="color: var(--text-muted); font-weight: 600;"><?php echo $client['id']; ?></td>
                                    <td>
                                        <a href="clientSales.php?client_id=<?php echo $client['id']; ?>" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                            <?php echo $client['name']; ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light px-3 py-2" style="border-radius: 0.5rem; font-weight: 600;">
                                            <?php echo $client['purchase_count']; ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: #dc2626;">$<?php echo number_format($client['debt'], 2); ?></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="sales.php?client_id=<?php echo $client['id']; ?>" class="action-btn" style="background: #f0fdf4; color: #166534;" title="Nueva Venta">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                            </a>
                                            <button class="action-btn" style="background: #eff6ff; color: #1e40af;" data-toggle="modal" data-target="#editClientModal<?php echo $client['id']; ?>" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </button>
                                            <a href="../../backend/controllers/deleteClientHandler.php?id=<?php echo $client['id']; ?>" class="action-btn" style="background: #fef2f2; color: #991b1b;" onclick="return confirm('¿Estás seguro?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Editar (Simplificado y Modernizado) -->
                                <div class="modal fade" id="editClientModal<?php echo $client['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title font-weight-bold">Editar Cliente</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="../../backend/controllers/updateClientHandler.php" method="post">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                                    <div class="form-group mb-3">
                                                        <label class="small font-weight-bold text-muted">Nombre Completo</label>
                                                        <input type="text" class="form-control modern-input" name="name" value="<?php echo $client['name']; ?>" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="small font-weight-bold text-muted">Teléfono</label>
                                                                <input type="text" class="form-control modern-input" name="phone" value="<?php echo $client['phone']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="small font-weight-bold text-muted">CUIT</label>
                                                                <input type="text" class="form-control modern-input" name="cuit" value="<?php echo $client['cuit']; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="small font-weight-bold text-muted">Email</label>
                                                        <input type="email" class="form-control modern-input" name="email" value="<?php echo $client['email']; ?>" required>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small font-weight-bold text-muted">Dirección</label>
                                                        <input type="text" class="form-control modern-input" name="address" value="<?php echo $client['address']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 0.75rem;">Cerrar</button>
                                                    <button type="submit" class="modern-btn modern-btn-primary px-4" style="width: auto;">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#clientTable').DataTable({
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
        });

        document.getElementById('printPdfBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Listado de Clientes Con Deudas", 14, 10);
            const tableColumns = ['ID', 'Nombre', 'Compras', 'Deuda'];
            const tableRows = <?php echo json_encode(array_map(function($client) {
                return [ $client['id'], $client['name'], $client['purchase_count'], '$' . number_format($client['debt'], 2) ];
            }, $clients)); ?>;
            doc.autoTable({ startY: 20, head: [tableColumns], body: tableRows, theme: 'grid' });
            doc.save('listado_clientes_deuda.pdf');
        });
    </script>
</body>
</body>
</html>
