<?php
/**
 * Admin: Listar Fornecedores
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Fornecedores';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Fornecedores'],
];

$por_pagina   = 20;
$busca        = trim($_GET['busca'] ?? '');
$where        = '1=1';
$params       = [];
if ($busca) {
    $where    = '(nome_fantasia LIKE ? OR razao_social LIKE ? OR cnpj LIKE ?)';
    $like     = '%' . $busca . '%';
    $params   = [$like, $like, $like];
}
$total        = db_count("SELECT COUNT(*) FROM tab_fornecedores WHERE $where", $params);
$pag          = paginate($total, $por_pagina);
$fornecedores = db_fetch_all(
    "SELECT * FROM tab_fornecedores WHERE $where ORDER BY razao_social LIMIT ? OFFSET ?",
    [...$params, $por_pagina, $pag['offset']]
);

ob_start();
?>
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Fornecedores</h1>
        <p class="page-subtitle">Cadastro de empresas e prestadores de serviços parceiros (<?= $total ?> registros)</p>
    </div>
</div>

<!-- BUSCA E FILTROS -->
<div class="card mb-4" style="padding: 1rem 1.25rem;">
    <form method="GET" class="filter-bar">
        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; width:100%;">
            <input type="text" name="busca" class="form-input form-input-sm" value="<?= e($busca) ?>" placeholder="🔍 Buscar por Razão Social, Nome Fantasia ou CNPJ..." style="min-width:280px; flex-grow:1; max-width:450px;">
            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <?php if ($busca): ?>
            <a href="<?= base_url('/admin/fornecedores/listar.php') ?>" class="btn btn-ghost btn-sm">Limpar Busca</a>
            <?php endif; ?>
            <span class="filter-count"><?= $total ?> fornecedor(es)</span>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Razão Social / Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Contato</th>
                    <th>Telefone</th>
                    <th>UF</th>
                    <th>Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fornecedores)): ?>
                <tr><td colspan="6" class="text-center text-muted py-8">Nenhum fornecedor cadastrado.</td></tr>
                <?php else: ?>
                <?php foreach ($fornecedores as $f): ?>
                <tr class="row-expandable" data-expand-id="expand-<?= $f['id'] ?>" title="Clique para ver detalhes do endereço e e-mail">
                    <td>
                        <strong><?= e($f['razao_social'] ?: ($f['nome_fantasia'] ?: 'Empresa sem nome')) ?></strong>
                        <?php if ($f['nome_fantasia'] && $f['razao_social']): ?>
                            <br><small class="text-muted"><?= e($f['nome_fantasia']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= e($f['cnpj'] ?: '—') ?></td>
                    <td>
                        <?= e($f['contato_nome'] ?: '—') ?>
                        <?php if ($f['contato_cargo']): ?><br><small class="text-muted"><?= e($f['contato_cargo']) ?></small><?php endif; ?>
                    </td>
                    <td><?= e($f['contato_telefone'] ?: '—') ?></td>
                    <td>
                        <?php if (!empty($f['estado'])): ?>
                            <span class="badge badge-secondary"><?= e($f['estado']) ?></span>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><span class="text-muted text-sm"><?= $f['created_at'] ? format_date(substr($f['created_at'], 0, 10)) : '—' ?></span></td>
                </tr>
                <!-- Linha de detalhes expansível -->
                <tr class="row-details" id="expand-<?= $f['id'] ?>" style="display:none">
                    <td colspan="6">
                        <div class="row-details-inner">
                            <p><strong>📍 Endereço:</strong> <?= e($f['endereco'] ?: 'Não informado') ?><?= !empty($f['bairro']) ? ', ' . e($f['bairro']) : '' ?><?= !empty($f['cidade']) ? ' — ' . e($f['cidade']) : '' ?><?= !empty($f['estado']) ? '/' . e($f['estado']) : '' ?><?= !empty($f['cep']) ? ' (CEP: ' . e($f['cep']) . ')' : '' ?></p>
                            <p><strong>✉️ E-mail:</strong> <?= !empty($f['contato_email']) ? '<a href="mailto:' . e($f['contato_email']) . '" style="color:var(--adm-accent-light);">' . e($f['contato_email']) . '</a>' : 'Não informado' ?></p>
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
            <?= pagination_html($pag, base_url('/admin/fornecedores/listar.php') . '?busca=' . urlencode($busca)) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.row-expandable').forEach(row => {
    row.addEventListener('click', () => {
        const targetId = row.dataset.expandId;
        const details = document.getElementById(targetId);
        if (details) details.style.display = details.style.display === 'none' ? 'table-row' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
