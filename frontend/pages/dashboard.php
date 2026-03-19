<?php
session_start();



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Distribuidora Julio Iturraspe</title>
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout {
            display: flex;
            min-height: 100vh;
        }
        .content-area {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            background-color: #f8fafc;
        }
        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: var(--shadow-soft);
            transition: all 0.2s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-strong);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .grid-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        @media (max-width: 992px) {
            .content-area { margin-left: 0; }
        }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="container-fluid mt-4">
                <div class="grid-dashboard">
                    <!-- Productos -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.125rem;">Inventario</h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">Gestiona tus productos, precios y alertas de stock.</p>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="productList.php" class="modern-btn modern-btn-primary" style="font-size: 0.875rem; padding: 0.5rem;">Ver Productos</a>
                            <a href="uploadPrices.php" class="modern-btn" style="font-size: 0.875rem; padding: 0.5rem; border: 1px solid #e2e8f0; background: white; color: var(--text-main);">Actualizar Precios</a>
                        </div>
                    </div>

                    <!-- Clientes -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.125rem;">Clientes</h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">Administra tu cartera de clientes y cuentas corrientes.</p>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="clientList.php" class="modern-btn modern-btn-primary" style="background: #10b981; font-size: 0.875rem; padding: 0.5rem;">Lista de Clientes</a>
                            <a href="clientListDeb.php" class="modern-btn" style="font-size: 0.875rem; padding: 0.5rem; border: 1px solid #e2e8f0; background: white; color: var(--text-main);">Cuentas con Deuda</a>
                        </div>
                    </div>

                    <!-- Ventas -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.125rem;">Ventas</h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">Registra pedidos y revisa el historial de transacciones.</p>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="salesList.php" class="modern-btn modern-btn-primary" style="background: #f59e0b; font-size: 0.875rem; padding: 0.5rem;">Historial de Ventas</a>
                            <a href="payments.php" class="modern-btn" style="font-size: 0.875rem; padding: 0.5rem; border: 1px solid #e2e8f0; background: white; color: var(--text-main);">Cobros Recibidos</a>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                        </div>
                        <h4 style="font-weight: 700; font-size: 1.125rem;">Análisis</h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">Visualiza el rendimiento de tu negocio con estadísticas.</p>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="statistics.php" class="modern-btn modern-btn-primary" style="background: #8b5cf6; font-size: 0.875rem; padding: 0.5rem;">Ver Estadísticas</a>
                            <a href="productReport.php" class="modern-btn" style="font-size: 0.875rem; padding: 0.5rem; border: 1px solid #e2e8f0; background: white; color: var(--text-main);">Reporte de Productos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

   
</body>
</html>
