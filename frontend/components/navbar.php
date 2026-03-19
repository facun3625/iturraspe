<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #002395; padding-left: 100px; padding-right: 100px; z-index: 1000;">
    <a class="navbar-brand" href="../pages/dashboard.php">
       
        Julio Iturraspe Distribuidora
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../pages/dashboard.php">Inicio</a>
            </li>
            <!-- Categorías con submenú -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Categorías
                </a>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="categoriesDropdown">
                    <a class="dropdown-item" href="../pages/addCategory.php">Agregar Categoría</a>
                    <a class="dropdown-item" href="../pages/categoryList.php">Ver Categorías</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Productos
                </a>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="productsDropdown">
                    <a class="dropdown-item" href="../pages/addProduct.php">Agregar Productos</a>
                    <a class="dropdown-item" href="../pages/uploadPrices.php">Actualizar Precios</a>
                    <a class="dropdown-item" href="../pages/productList.php">Ver Productos</a>
                    <a class="dropdown-item" href="../pages/productListLowStock.php">Alertas de Stock</a>
                    <a class="dropdown-item" href="../pages/productListHide.php">Ver Productos Eliminados</a>
                    <a class="dropdown-item" href="../pages/priceList.php">Lista de Precios</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="clientsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Clientes
                </a>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="clientsDropdown">
                    <a class="dropdown-item" href="../pages/addClient.php">Agregar Clientes</a>
                    <a class="dropdown-item" href="../pages/clientList.php">Clientes</a>
                    <a class="dropdown-item" href="../pages/clientListDeb.php">Clientes Con Deuda</a>
                    <a class="dropdown-item" href="../pages/clientListHide.php">Clientes Eliminados</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="salesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ventas
                </a>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="salesDropdown">
                    <a class="dropdown-item" href="../pages/salesList.php">Todas las Ventas</a>
                    <a class="dropdown-item" href="../pages/payments.php">Pagos Recibidos</a>
                    <a class="dropdown-item" href="../pages/productReport.php">Reportes de Ventas</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../pages/statistics.php">Estadísticas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../backend/controllers/logout.php">Cerrar sesión</a>
            </li>

        </ul>
    </div>
</nav>

<style>
/* Estilo para el dropdown con bordes redondeados y efecto fade-in */
.custom-dropdown {
    border-radius: 8px; /* Borde redondeado */
    opacity: 0;
    transform: translateY(10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    position: absolute;
    left: 0;
}

/* Mostrar el menú con alineación */
.dropdown-menu.show.custom-dropdown {
    opacity: 1;
    transform: translateY(0);
}

/* Ajustes adicionales para mantener el dropdown alineado */
.navbar .dropdown-menu {
    top: 100%;
    margin-top: 7px;
}
</style>
