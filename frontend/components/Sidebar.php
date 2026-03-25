<?php
// Sidebar.php - Navegación lateral con acordeón
$current_page = basename($_SERVER['PHP_SELF']);

function is_active($pages) {
    global $current_page;
    if (is_array($pages)) {
        return in_array($current_page, $pages) ? 'active' : '';
    }
    return $current_page == $pages ? 'active' : '';
}

function is_expanded($pages) {
    global $current_page;
    return in_array($current_page, $pages) ? 'expanded' : '';
}
?>
<aside class="modern-sidebar">
    <div class="sidebar-header">
        <button class="sidebar-close" id="sidebarClose">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
        </div>
        <span class="brand-name">Iturraspe</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <p class="section-title">Principal</p>
            <a href="dashboard.php" class="nav-item <?php echo is_active('dashboard.php'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="15" rx="1"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <p class="section-title">Negocio</p>
            
            <!-- Productos Accordion -->
            <?php $product_pages = ['productList.php', 'addProduct.php', 'uploadPrices.php', 'productListLowStock.php', 'productListHide.php', 'priceList.php', 'modifyProduct.php']; ?>
            <div class="nav-group <?php echo is_expanded($product_pages); ?>">
                <button class="nav-item group-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    <span>Productos</span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="nav-sub">
                    <a href="productList.php" class="sub-item <?php echo is_active('productList.php'); ?>">Ver Todos</a>
                    <a href="addProduct.php" class="sub-item <?php echo is_active('addProduct.php'); ?>">Agregar Nuevo</a>
                    <a href="uploadPrices.php" class="sub-item <?php echo is_active('uploadPrices.php'); ?>">Actualizar Precios</a>
                    <a href="productListLowStock.php" class="sub-item <?php echo is_active('productListLowStock.php'); ?>">Alertas Stock</a>
                    <a href="productListHide.php" class="sub-item <?php echo is_active('productListHide.php'); ?>">Eliminados</a>
                    <button onclick="copyPriceListLink(this)" class="sub-item" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <span class="icon-span">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        </span>
                        <span class="text-span">Compartir Lista</span>
                    </button>
                </div>
            </div>

            <!-- Categorías Accordion -->
            <?php $cat_pages = ['categoryList.php', 'addCategory.php']; ?>
            <div class="nav-group <?php echo is_expanded($cat_pages); ?>">
                <button class="nav-item group-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
                    <span>Categorías</span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="nav-sub">
                    <a href="categoryList.php" class="sub-item <?php echo is_active('categoryList.php'); ?>">Ver Todas</a>
                    <a href="addCategory.php" class="sub-item <?php echo is_active('addCategory.php'); ?>">Nueva Categoría</a>
                </div>
            </div>

            <!-- Clientes Accordion -->
            <?php $client_pages = ['clientList.php', 'addClient.php', 'clientListDeb.php', 'clientListHide.php']; ?>
            <div class="nav-group <?php echo is_expanded($client_pages); ?>">
                <button class="nav-item group-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Clientes</span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="nav-sub">
                    <a href="clientList.php" class="sub-item <?php echo is_active('clientList.php'); ?>">Ver Todos</a>
                    <a href="addClient.php" class="sub-item <?php echo is_active('addClient.php'); ?>">Nuevo Cliente</a>
                    <a href="clientListDeb.php" class="sub-item <?php echo is_active('clientListDeb.php'); ?>">Con Deuda</a>
                </div>
            </div>

            <!-- Ventas Accordion -->
            <?php $sale_pages = ['salesList.php', 'payments.php', 'productReport.php', 'statistics.php', 'operatingCosts.php']; ?>
            <div class="nav-group <?php echo is_expanded($sale_pages); ?>">
                <button class="nav-item group-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span>Operaciones</span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="nav-sub">
                    <a href="salesList.php" class="sub-item <?php echo is_active('salesList.php'); ?>">Ventas</a>
                    <a href="payments.php" class="sub-item <?php echo is_active('payments.php'); ?>">Pagos</a>
                    <a href="operatingCosts.php" class="sub-item <?php echo is_active('operatingCosts.php'); ?>">Costos Operativos</a>
                    <a href="statistics.php" class="sub-item <?php echo is_active('statistics.php'); ?>">Estadísticas</a>
                </div>
            </div>

            <!-- Gestión de Sistema -->
            <div class="nav-section">
                <div class="section-title">Sistema</div>
                <a href="profile.php" class="nav-item <?php echo is_active('profile.php'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Mi Perfil</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            <span>Cerrar Sesión</span>
        </a>
    </div>

</aside>

<!-- Top Bar para Móviles -->
<div class="mobile-top-bar">
    <button class="mobile-toggle" id="mobileToggle">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="mobile-brand-name">Iturraspe</div>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
const sidebar = document.querySelector('.modern-sidebar');
const mobileToggle = document.getElementById('mobileToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarClose = document.getElementById('sidebarClose');

if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    });
}

if (sidebarClose) {
    sidebarClose.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';
    });
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';
    });
}

// Acordiones
document.querySelectorAll('.group-trigger').forEach(trigger => {
    trigger.addEventListener('click', () => {
        const group = trigger.parentElement;
        group.classList.toggle('expanded');
    });
});

function copyPriceListLink(btn) {
    const baseUrl = window.location.origin;
    // Asumimos que lista.php está en la raíz. Si el proyecto está en una subcarpeta, 
    // necesitamos detectarla o configurarla.
    let path = window.location.pathname;
    let folder = path.substring(0, path.indexOf('/backend/'));
    const fullUrl = baseUrl + folder + '/lista.php';
    
    navigator.clipboard.writeText(fullUrl).then(() => {
        const textSpan = btn.querySelector('.text-span');
        const iconSpan = btn.querySelector('.icon-span');
        const originalText = textSpan.innerText;
        const originalIcon = iconSpan.innerHTML;
        
        textSpan.innerText = '¡Copiado!';
        textSpan.style.color = '#10b981';
        iconSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        
        setTimeout(() => {
            textSpan.innerText = originalText;
            textSpan.style.color = '';
            iconSpan.innerHTML = originalIcon;
        }, 2000);
    }).catch(err => {
        console.error('Error al copiar: ', err);
        alert('No se pudo copiar el enlace automáticamente.');
    });
}
</script>

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
    user-select: none;
}

.sidebar-header {
    padding: 2rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.brand-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.brand-name {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    color: #fff;
}

.sidebar-nav {
    flex: 1;
    padding: 0 0.75rem;
    overflow-y: auto;
}

.nav-section {
    margin-bottom: 1.5rem;
}

.section-title {
    padding-left: 0.75rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #475569;
    letter-spacing: 0.1em;
    margin-bottom: 0.75rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #94a3b8;
    text-decoration: none !important;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
    font-weight: 500;
    margin-bottom: 0.25rem;
    width: 100%;
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: left;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc;
}

.nav-item.active {
    background: #3b82f6;
    color: white;
}

.nav-group .chevron {
    margin-left: auto;
    transition: transform 0.3s ease;
    opacity: 0.5;
}

.nav-group.expanded .chevron {
    transform: rotate(180deg);
}

.nav-sub {
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    padding-left: 2.25rem;
    display: flex;
    flex-direction: column;
}

.nav-group.expanded .nav-sub {
    max-height: 500px;
    margin-bottom: 0.75rem;
}

.sub-item {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: #64748b;
    text-decoration: none !important;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    margin-bottom: 0.125rem;
}

.sub-item:hover {
    color: #f8fafc;
    background: rgba(255, 255, 255, 0.03);
}

.sub-item.active {
    color: #3b82f6;
    font-weight: 600;
}

.sidebar-footer {
    padding: 1.25rem;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #ef4444;
    text-decoration: none !important;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.logout-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* Scrollbar styling - More subtle */
.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}
.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}
.sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.sidebar-nav:hover::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
}
</style>
