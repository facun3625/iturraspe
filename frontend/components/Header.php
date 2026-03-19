<?php
// Header.php - Cabecera moderna
?>
<header class="modern-header">
    <div class="header-content">
        <div class="header-left">
            <h2 class="page-title"><?php echo isset($page_title) ? $page_title : 'Panel de Control'; ?></h2>
        </div>
        <div class="header-right">
            <div class="user-badge">
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                    <span class="user-role">Administrador</span>
                </div>
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.modern-header {
    height: 70px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 900;
    margin-left: 260px;
    padding: 0 2rem;
}

.header-content {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.user-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 9999px;
    background: #f1f5f9;
}

.user-info {
    display: flex;
    flex-direction: column;
    text-align: right;
    padding-left: 0.75rem;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
}

.user-role {
    font-size: 0.75rem;
    color: #64748b;
}

.avatar {
    width: 32px;
    height: 32px;
    background: #3b82f6;
    color: white;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
}

@media (max-width: 992px) {
    .modern-header {
        margin-left: 0;
    }
}
</style>
