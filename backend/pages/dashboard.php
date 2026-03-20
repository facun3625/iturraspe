<?php
session_start();



?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Julio Iturraspe</title>
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { 
            flex: 1; 
            margin-left: 260px; 
            padding: 3rem; 
            background: #f1f5f9;
            min-width: 0;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem; /* Increased gap to prevent cards from being 'pegados' */
            width: 100%;
        }
        .card-modern {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 2.5rem; /* Increased padding */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border: 1px solid rgba(226, 232, 240, 0.8);
            width: 100%;
            overflow: hidden;
            box-sizing: border-box;
        }
        .card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }
        .card-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            flex-grow: 1;
        }
        .action-btns {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .secondary-grid {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-ghost {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 0.8rem;
            padding: 0.75rem;
            border-radius: 0.75rem;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: block;
        }
        .btn-ghost:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        .header-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 3rem;
            letter-spacing: -0.05em;
        }
        @media (max-width: 992px) { .content-area { margin-left: 0; padding: 1.5rem; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <h1 class="header-title">Panel de Control</h1>

            <div class="dashboard-grid">
                <!-- Ventas -->
                <div class="card-modern">
                    <div class="icon-box" style="background: #fff7ed; color: #f59e0b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <h2 class="card-title">Ventas</h2>
                    <p class="card-subtitle">Registrá pedidos y gestioná los cobros de tus clientes de forma eficiente.</p>
                    <div class="action-btns">
                        <a href="clientList.php" class="modern-btn" style="background: #f59e0b; color: white;">Iniciar Venta</a>
                        <div class="secondary-grid">
                            <a href="salesList.php" class="btn-ghost">Historial</a>
                            <a href="payments.php" class="btn-ghost">Pagos</a>
                        </div>
                    </div>
                </div>

                <!-- Inventario -->
                <div class="card-modern">
                    <div class="icon-box" style="background: #eff6ff; color: #3b82f6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                    <h2 class="card-title">Inventario</h2>
                    <p class="card-subtitle">Mantené el catálogo actualizado y controlá el stock de tus productos.</p>
                    <div class="action-btns">
                        <a href="productList.php" class="modern-btn modern-btn-primary">Ver Productos</a>
                        <a href="uploadPrices.php" class="btn-ghost">Actualizar Precios</a>
                    </div>
                </div>

                <!-- Clientes -->
                <div class="card-modern">
                    <div class="icon-box" style="background: #f0fdf4; color: #10b981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h2 class="card-title">Clientes</h2>
                    <p class="card-subtitle">Administrá tu cartera de clientes y accedé a sus estados de cuenta detallados.</p>
                    <div class="action-btns">
                        <a href="clientList.php" class="modern-btn" style="background: #10b981; color: white;">Directorio</a>
                        <a href="clientListDeb.php" class="btn-ghost">Ver Deudores</a>
                    </div>
                </div>

                <!-- Análisis -->
                <div class="card-modern">
                    <div class="icon-box" style="background: #f5f3ff; color: #8b5cf6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                    </div>
                    <h2 class="card-title">Análisis</h2>
                    <p class="card-subtitle">Visualizá el crecimiento del negocio mediante reportes y estadísticas.</p>
                    <div class="action-btns">
                        <a href="statistics.php" class="modern-btn" style="background: #8b5cf6; color: white;">Estadísticas</a>
                        <a href="productReport.php" class="btn-ghost">Reporte Final</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html></body>
</html>
