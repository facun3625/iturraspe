<?php
// Sidebar.php - Componente de navegación lateral moderno
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="modern-sidebar">
    <div class="sidebar-header">
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
        </div>
        <span class="brand-name">Iturraspe</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <p class="section-title">Principal</p>
            <a href="dashboard.php" class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="15" rx="1"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <p class="section-title">Gestión</p>
            <a href="productList.php" class="nav-item <?php echo $current_page == 'productList.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                <span>Productos</span>
            </a>
            <a href="categoryList.php" class="nav-item <?php echo $current_page == 'categoryList.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
                <span>Categorías</span>
            </a>
            <a href="clientList.php" class="nav-item <?php echo $current_page == 'clientList.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Clientes</span>
            </a>
        </div>

        <div class="nav-section">
            <p class="section-title">Operaciones</p>
            <a href="salesList.php" class="nav-item <?php echo $current_page == 'salesList.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                <span>Ventas</span>
            </a>
            <a href="statistics.php" class="nav-item <?php echo $current_page == 'statistics.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                <span>Estadísticas</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="../../backend/controllers/logout.php" class="logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>

<style>
.modern-sidebar {
    width: 260px;
    height: 100vh;
    background: #0f172a;
    color: #f8fafc;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    transition: all 0.3s ease;
}

.sidebar-header {
    padding: 2rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.brand-icon {
    width: 32px;
    height: 32px;
    background: #3b82f6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.brand-name {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.sidebar-nav {
    flex: 1;
    padding: 0 0.75rem;
    overflow-y: auto;
}

.nav-section {
    margin-bottom: 2rem;
}

.section-title {
    padding-left: 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #475569;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #94a3b8;
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc;
    text-decoration: none;
}

.nav-item.active {
    background: #3b82f6;
    color: white;
}

.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #ef4444;
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.logout-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    text-decoration: none;
}
</style>
