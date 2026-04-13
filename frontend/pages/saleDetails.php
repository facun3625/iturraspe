<?php
// Ruta: control_stock/frontend/pages/saleDetails.php

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

require_once '../../backend/controllers/saleController.php';

if (!isset($_GET['sale_id'])) {
    die("ID de venta no especificado.");
}

$sale_id = $_GET['sale_id'];
$saleController = new SaleController();
$saleDetails = $saleController->getSaleDetails($sale_id);

if (!$saleDetails) {
    die("No se encontró la venta especificada.");
}

// Asignación de los valores necesarios
$totalPaid = $saleDetails['amount_paid'] ?? 0; // Total pagado
$totalDebt = $saleDetails['sale_total'] - $totalPaid; // Diferencia para obtener la deuda
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta #<?php echo $sale_id; ?> | Julio Iturraspe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css?v=1.1">
    <style>
        .receipt-card { background: white; border-radius: 1.5rem; box-shadow: var(--shadow-soft); overflow: hidden; max-width: 900px; margin: 0 auto; border: 1px solid #f1f5f9; }
        .receipt-header { background: #f8fafc; padding: 2rem; border-bottom: 2px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .receipt-body { padding: 2.5rem; }
        .receipt-footer { background: #f8fafc; padding: 2rem; border-top: 2px dashed #e2e8f0; }

        .client-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem; }
        .client-name { font-size: 1.5rem; font-weight: 800; color: var(--text-main); }
        
        .item-row { border-bottom: 1px solid #f1f5f9; padding: 1rem 0; }
        .item-row:last-child { border-bottom: none; }
        
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; }
        .summary-label { font-weight: 600; color: #64748b; }
        .summary-value { font-weight: 700; color: var(--text-main); }
        
        .badge-discount { background: #f0fdf4; color: #166534; padding: 0.25rem 0.5rem; border-radius: 0.5rem; font-size: 0.7rem; font-weight: 700; }
    </style>
le>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>

        <div class="content-area">
            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4" style="max-width: 900px; margin: 0 auto 2rem;">
                <button class="modern-btn" onclick="window.history.back()" style="width: auto; background: white; color: var(--text-muted); border: 1px solid #e1e4e8; padding: 0.6rem 1.2rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="m15 18-6-6 6-6"/></svg>
                    Volver
                </button>
                <div class="d-flex gap-2">
                    <a href="salesEdit.php?sale_id=<?php echo $sale_id; ?>" class="modern-btn" style="width: auto; background: #fff; color: #92400e; border: 1px solid #fef3c7; padding: 0.6rem 1.2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar Venta
                    </a>
                    <button id="printPdfBtn" class="modern-btn modern-btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: #ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        PDF
                    </button>
                </div>
            </div>

            <div class="receipt-card">
                <div class="receipt-header">
                    <div>
                        <span class="badge badge-primary px-3 py-1 mb-2" style="border-radius: 2rem; font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Comprobante de Venta</span>
                        <h2 style="font-weight: 800; color: var(--text-main); margin: 0;">Venta #<?php echo $sale_id; ?></h2>
                        <span style="color: #64748b; font-weight: 600; font-size: 0.9rem;"><?php echo date('d/m/Y H:i', strtotime($saleDetails['sale_date'])); ?></span>
                    </div>
                    <div class="text-right">
                        <span class="client-label">CLIENTE</span>
                        <span class="client-name"><?php echo htmlspecialchars($saleDetails['client_name']); ?></span>
                    </div>
                </div>

                <div class="receipt-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr style="border-bottom: 2px solid #f1f5f9;">
                                    <th style="color: #64748b; font-size: 0.75rem; font-weight: 700;">COD.</th>
                                    <th style="color: #64748b; font-size: 0.75rem; font-weight: 700;">PRODUCTO</th>
                                    <th class="text-center" style="color: #64748b; font-size: 0.75rem; font-weight: 700;">CANT.</th>
                                    <th class="text-right" style="color: #64748b; font-size: 0.75rem; font-weight: 700;">P. LISTA</th>
                                    <th class="text-center" style="color: #64748b; font-size: 0.75rem; font-weight: 700;">DESC.</th>
                                    <th class="text-right" style="color: #64748b; font-size: 0.75rem; font-weight: 700;">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($saleDetails['items'] as $item): 
                                    $discount = floatval($item['discount'] ?? 0);
                                    $finalPrice = floatval($item['price']);
                                    $originalPrice = ($discount < 100) ? ($finalPrice / (1 - ($discount / 100))) : $finalPrice;
                                    $subtotal = $item['quantity'] * $finalPrice;
                                ?>
                                    <tr class="item-row">
                                        <td style="font-weight: 600; color: var(--text-muted); white-space: nowrap;"><?php echo htmlspecialchars($item['product_cod']); ?></td>
                                        <td>
                                            <span style="display: block; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                        </td>
                                        <td class="text-center" style="font-weight: 600; color: var(--text-muted);">x<?php echo $item['quantity']; ?></td>
                                        <td class="text-right">
                                            <span style="font-weight: 600; color: #64748b; font-size: 0.9rem;">$<?php echo number_format($originalPrice, 2); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($discount > 0): ?>
                                                <span class="badge-discount" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">-<?php echo $discount; ?>%</span>
                                            <?php else: ?>
                                                <span style="color: #cbd5e1;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right" style="font-weight: 800; color: var(--primary); font-size: 1.1rem;">$<?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="receipt-footer">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="summary-row" style="font-size: 1.1rem;">
                                <span class="summary-label">TOTAL VENTA</span>
                                <span class="summary-value" style="color: var(--primary); font-size: 1.5rem; font-weight: 800;">$<?php echo number_format($saleDetails['sale_total'], 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Monto Pagado</span>
                                <span class="summary-value" style="color: #10b981;">$<?php echo number_format($totalPaid, 2); ?></span>
                            </div>
                            <div class="summary-row" style="border-top: 1px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.75rem;">
                                <span class="summary-label">Saldo Pendiente</span>
                                <span class="summary-value" style="color: #ef4444;">$<?php echo number_format($totalDebt, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('printPdfBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Header
            doc.setFontSize(22);
            doc.setTextColor(30, 41, 59); // var(--text-main)
            doc.text("Julio Iturraspe", 14, 25);
            
            doc.setFontSize(14);
            doc.text(`Comprobante de Venta #<?php echo $sale_id; ?>`, 14, 35);
            
            doc.setFontSize(10);
            doc.setTextColor(100, 116, 139); // var(--text-muted)
            doc.text(`Fecha: ${new Date('<?php echo $saleDetails['sale_date']; ?>').toLocaleString()}`, 14, 42);
            doc.text(`Cliente: <?php echo $saleDetails['client_name']; ?>`, 14, 48);

            // Table
            const tableColumns = ['Cod.', 'Producto', 'Cant.', 'P. Lista', 'Desc.', 'Subtotal'];
            const tableRows = <?php echo json_encode(array_map(function($item) {
                $discount = floatval($item['discount'] ?? 0);
                $finalPrice = floatval($item['price']);
                $originalPrice = ($discount < 100) ? ($finalPrice / (1 - ($discount / 100))) : $finalPrice;
                return [
                    $item['product_cod'],
                    $item['product_name'],
                    $item['quantity'],
                    '$' . number_format($originalPrice, 2),
                    ($discount > 0 ? '-' . $discount . '%' : '-'),
                    '$' . number_format($item['quantity'] * $finalPrice, 2)
                ];
            }, $saleDetails['items'])); ?>;

            doc.autoTable({
                startY: 60,
                head: [tableColumns],
                body: tableRows,
                theme: 'grid',
                headStyles: { fillColor: [59, 130, 246] },
                columnStyles: {
                    0: { halign: 'center' },
                    2: { halign: 'center' },
                    3: { halign: 'right' },
                    4: { halign: 'center' },
                    5: { halign: 'right' }
                }
            });

            const finalY = doc.lastAutoTable.finalY + 15;
            
            // Summary
            doc.setFontSize(12);
            doc.setTextColor(30, 41, 59);
            doc.text(`TOTAL VENTA: $<?php echo number_format($saleDetails['sale_total'], 2); ?>`, 140, finalY, { align: 'left' });
            doc.text(`Pagado: $<?php echo number_format($totalPaid, 2); ?>`, 140, finalY + 7, { align: 'left' });
            doc.setTextColor(239, 68, 68);
            doc.text(`Saldo Pendiente: $<?php echo number_format($totalDebt, 2); ?>`, 140, finalY + 14, { align: 'left' });

            doc.save('venta_<?php echo $sale_id; ?>_<?php echo str_replace(' ', '_', $saleDetails['client_name']); ?>.pdf');
        });
    </script>
</body>
</html>
