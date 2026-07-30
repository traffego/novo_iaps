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
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Alternar Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        
        <nav aria-label="breadcrumb">
            <div class="admin-breadcrumb">
                <a href="/admin">Admin</a>
                <?php if (!empty($breadcrumb) && is_array($breadcrumb)): ?>
                    <?php foreach ($breadcrumb as $item): ?>
                        <span class="sep">/</span>
                        <?php if (!empty($item['url'])): ?>
                            <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
                        <?php else: ?>
                            <span class="current"><?= e($item['label']) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php elseif (isset($breadcrumb_title)): ?>
                    <span class="sep">/</span>
                    <span class="current"><?= htmlspecialchars($breadcrumb_title) ?></span>
                <?php endif; ?>
            </div>
        </nav>
    </div>
    
    <div class="admin-header-right">
        <button id="admin-theme-toggle" class="btn btn-ghost btn-sm" title="Alternar Tema Claro/Escuro" style="padding:0.4rem 0.6rem; border-radius: var(--adm-radius-full);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </button>

        <a href="/" target="_blank" class="btn btn-ghost btn-sm" title="Ver Site Público">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            <span>Ver Site</span>
        </a>
        
        <div class="dropdown" id="user-menu-dropdown">
            <div class="user-dropdown">
                <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
                <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            
            <div class="dropdown-menu">
                <a href="/admin/configuracoes" class="dropdown-item">Minha Conta</a>
                <a href="/admin/logout.php" class="dropdown-item text-danger">Sair do Sistema</a>
            </div>
        </div>
    </div>
</header>
