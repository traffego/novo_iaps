<?php
/**
 * Admin: Listar Notícias
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Notícias';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Notícias'],
];

$por_pagina = 20;
$total     = db_count('SELECT COUNT(*) FROM tab_noticias');
$pag       = paginate($total, $por_pagina);
$noticias  = db_fetch_all('SELECT * FROM tab_noticias ORDER BY data_noticia DESC LIMIT ? OFFSET ?', [$por_pagina, $pag['offset']]);

ob_start();
?>
<div class="page-top-bar">
    <a href="<?= base_url('/admin/noticias/criar.php') ?>" class="btn btn-primary">+ Nova Notícia</a>
</div>

<div class="admin-card">
    <input type="text" id="busca-tabela" class="form-input" placeholder="🔍 Pesquisar notícias..." style="max-width:340px;margin-bottom:1rem">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Manchete</th>
                <th>Data</th>
                <th>Visitas</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($noticias)): ?>
            <tr><td colspan="5" class="text-center text-muted">Nenhuma notícia cadastrada.</td></tr>
            <?php else: ?>
            <?php foreach ($noticias as $n): ?>
            <tr>
                <td><?= (int)$n['id'] ?></td>
                <td><?= e(truncate($n['manchete'], 70)) ?></td>
                <td><?= $n['data_noticia'] ? format_date($n['data_noticia']) : '—' ?></td>
                <td><?= number_format((int)$n['n_visitas'], 0, ',', '.') ?></td>
                <td class="actions">
                    <a href="<?= base_url('/admin/noticias/editar.php?id=' . $n['id']) ?>" class="btn btn-outline btn-sm">Editar</a>
                    <form method="POST" action="<?= base_url('/admin/noticias/excluir.php') ?>" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="Excluir esta notícia?">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if ($pag['total_pages'] > 1): echo pagination_html($pag, base_url('/admin/noticias/listar.php')); endif; ?>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
