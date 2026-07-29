<?php
// templates/admin/layout.php
if (!defined('ROOT_PATH')) exit;

$page_title = $page_title ?? 'Admin - IAPS';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="admin-layout" id="admin-app">
        
        <!-- Sidebar -->
        <?php include ROOT_PATH . '/templates/admin/sidebar.php'; ?>
        
        <!-- Overlay for mobile sidebar -->
        <div class="admin-overlay" id="sidebar-overlay"></div>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <?php include ROOT_PATH . '/templates/admin/header.php'; ?>
            
            <!-- Content -->
            <main class="admin-content">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'] ?? 'info') ?> fade-in visible">
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                    </div>
                    <?php 
                        unset($_SESSION['flash_message']); 
                        unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <?php if (isset($content)) echo $content; ?>
            </main>
            
            <footer class="p-6 border-t text-center text-muted text-sm" style="border-color: var(--border-color)">
                &copy; <?= date('Y') ?> Admin - Instituto Atleta Para Sempre
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Sidebar toggle logic
            const toggleBtn = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const app = document.getElementById('admin-app');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    if (window.innerWidth <= 992) {
                        app.classList.toggle('sidebar-open');
                    } else {
                        app.classList.toggle('sidebar-collapsed');
                    }
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', () => {
                    app.classList.remove('sidebar-open');
                });
            }

            // Init TinyMCE
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '.tinymce-editor',
                    menubar: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    content_style: 'body { font-family: Inter,sans-serif; font-size:16px }'
                });
            }
        });
    </script>
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
