<?php
/**
 * Layout base do site público
 * Instituto Atleta Para Sempre
 * 
 * Variáveis esperadas:
 * - $page_title (string)
 * - $page_description (string) 
 * - $content (string - HTML capturado via ob_start/ob_get_clean)
 */

$site_name = 'Instituto Atleta Para Sempre';
$full_title = isset($page_title) ? e($page_title) . ' | ' . $site_name : $site_name;
$meta_desc = $page_description ?? 'Instituto Atleta Para Sempre - Promovendo inclusão social através do esporte.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#00f2fe">
    <title><?= $full_title ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- GLightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">

    <!-- CSS Principal (Glacier Design) -->
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>?v=<?= file_exists(ROOT_PATH . '/assets/css/style.css') ? filemtime(ROOT_PATH . '/assets/css/style.css') : time() ?>">

    <!-- Script de Inicialização de Tema para Evitar Flicker -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="dark">
    <?php include ROOT_PATH . '/templates/header.php'; ?>

    <main id="main-content">
        <!-- Flash Messages -->
        <?php if ($flash_success = flash('success')): ?>
            <div class="flash-message flash-success" role="alert">
                <i data-lucide="check-circle-2"></i>
                <span><?= e($flash_success) ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($flash_error = flash('error')): ?>
            <div class="flash-message flash-error" role="alert">
                <i data-lucide="alert-triangle"></i>
                <span><?= e($flash_error) ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <?php include ROOT_PATH . '/templates/footer.php'; ?>

    <!-- Back to Top -->
    <button id="back-to-top" class="back-to-top" aria-label="Voltar ao topo" title="Voltar ao topo">
        <i data-lucide="chevron-up"></i>
    </button>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>

    <!-- Main JS -->
    <script src="<?= asset('assets/js/main.js') ?>?v=<?= file_exists(ROOT_PATH . '/assets/js/main.js') ? filemtime(ROOT_PATH . '/assets/js/main.js') : time() ?>"></script>

    <script>
        // Lucide Icons init
        function initIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        }
        initIcons();

        // GLightbox init
        if (typeof GLightbox !== 'undefined') {
            GLightbox({ selector: '.glightbox' });
        }

        // Flash messages auto-dismiss
        document.querySelectorAll('.flash-message').forEach(el => {
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 5000);
        });
    </script>
</body>
</html>
