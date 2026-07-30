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
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Notícias</h1>
        <p class="page-subtitle">Gerenciamento de artigos e publicações no site (<?= $total ?> cadastradas)</p>
    </div>
    <a href="<?= base_url('/admin/noticias/criar.php') ?>" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>Nova Notícia</span>
    </a>
</div>

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--adm-border);">
        <input type="text" id="busca-tabela" class="form-input form-input-sm" placeholder="🔍 Pesquisar notícias..." style="max-width:340px;">
    </div>
    <div class="table-responsive">
        <table class="data-table" id="tabela-noticias">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Manchete</th>
                    <th>Data</th>
                    <th>Visitas</th>
                    <th style="width:160px; text-align:right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($noticias)): ?>
                <tr><td colspan="5" class="text-center text-muted py-8">Nenhuma notícia cadastrada ainda.</td></tr>
                <?php else: ?>
                <?php foreach ($noticias as $n): ?>
                <tr>
                    <td><span class="text-muted text-sm">#<?= (int)$n['id'] ?></span></td>
                    <td><strong><?= e($n['manchete']) ?></strong></td>
                    <td><span class="text-muted text-sm"><?= $n['data_noticia'] ? format_date($n['data_noticia']) : '—' ?></span></td>
                    <td><span class="badge badge-secondary"><?= number_format((int)($n['n_visitas'] ?? 0), 0, ',', '.') ?> visitas</span></td>
                    <td>
                        <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                            <a href="<?= base_url('/admin/noticias/editar.php?id=' . $n['id']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <form method="POST" action="<?= base_url('/admin/noticias/excluir.php') ?>" style="display:inline" onsubmit="return confirm('Confirma a exclusão desta notícia?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                                <button type="submit" class="btn btn-ghost text-danger btn-sm">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pag['total_pages'] > 1): ?>
        <div style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--adm-border);">
            <?= pagination_html($pag, base_url('/admin/noticias/listar.php')) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('busca-tabela')?.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabela-noticias tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
