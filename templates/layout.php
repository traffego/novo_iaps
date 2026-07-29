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
    <meta name="theme-color" content="#059669">
    <title><?= $full_title ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- GLightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">

    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('imgs/favicon.ico') ?>">
</head>
<body class="dark">
    <?php include ROOT_PATH . '/templates/header.php'; ?>

    <main id="main-content">
        <!-- Flash Messages -->
        <?php if ($flash_success = flash('success')): ?>
            <div class="flash-message flash-success" role="alert">
                <span class="flash-icon">✓</span>
                <span><?= e($flash_success) ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($flash_error = flash('error')): ?>
            <div class="flash-message flash-error" role="alert">
                <span class="flash-icon">!</span>
                <span><?= e($flash_error) ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <?php include ROOT_PATH . '/templates/footer.php'; ?>

    <!-- Back to Top -->
    <button id="back-to-top" class="back-to-top" aria-label="Voltar ao topo" title="Voltar ao topo">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>

    <!-- Main JS -->
    <script src="<?= asset('js/main.js') ?>"></script>

    <script>
        // Tema dark/light
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark');
                const isDark = document.body.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }

        // Restaurar tema salvo
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.remove('dark');
        }

        // Back to top
        const backToTop = document.getElementById('back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', () => {
                backToTop.classList.toggle('visible', window.scrollY > 300);
            });
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // GLightbox init
        if (typeof GLightbox !== 'undefined') {
            GLightbox({ selector: '.glightbox' });
        }

        // Flash messages auto-dismiss
        document.querySelectorAll('.flash-message').forEach(el => {
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 5000);
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
