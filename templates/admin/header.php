<?php
// templates/admin/header.php
if (!defined('ROOT_PATH')) exit;

$user_name = $_SESSION['admin_nome'] ?? 'Administrador';
// Extract initials
$words = explode(' ', $user_name);
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
?>
<header class="admin-header">
    <div class="admin-header-left">
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        
        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <?php if (isset($breadcrumb_title)): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($breadcrumb_title) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
    
    <div class="admin-header-right">
        <a href="/" target="_blank" class="btn btn-ghost btn-sm" title="Ver Site">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            <span class="hidden sm:inline-block ml-2">Ver Site</span>
        </a>
        
        <div class="dropdown">
            <div class="user-dropdown" data-toggle="dropdown">
                <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
                <span class="hidden sm:inline-block font-medium text-sm ml-2"><?= htmlspecialchars($user_name) ?></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            
            <div class="dropdown-menu" style="right: 0; left: auto;">
                <a href="/admin/configuracoes" class="dropdown-item">Minha Conta</a>
                <a href="/admin/logout.php" class="dropdown-item text-danger">Sair do Sistema</a>
            </div>
        </div>
    </div>
</header>
