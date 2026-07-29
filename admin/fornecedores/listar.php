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
<!-- BUSCA -->
<div class="admin-card" style="margin-bottom:1rem">
    <form method="GET" class="filter-bar">
        <input type="text" name="busca" class="form-input form-input-sm" value="<?= e($busca) ?>" placeholder="🔍 Buscar por nome ou CNPJ...">
        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        <?php if ($busca): ?><a href="<?= base_url('/admin/fornecedores/listar.php') ?>" class="btn btn-ghost btn-sm">Limpar</a><?php endif; ?>
        <span class="filter-count"><?= $total ?> resultado(s)</span>
    </form>
</div>

<div class="admin-card">
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
                <tr><td colspan="6" class="text-center text-muted">Nenhum fornecedor encontrado.</td></tr>
                <?php else: ?>
                <?php foreach ($fornecedores as $f): ?>
                <tr class="row-expandable" data-expand-id="expand-<?= $f['id'] ?>">
                    <td>
                        <strong><?= e($f['razao_social']) ?></strong>
                        <?php if ($f['nome_fantasia']): ?><br><small class="text-muted"><?= e($f['nome_fantasia']) ?></small><?php endif; ?>
                    </td>
                    <td><?= e($f['cnpj']) ?></td>
                    <td>
                        <?= e($f['contato_nome']) ?>
                        <?php if ($f['contato_cargo']): ?><br><small class="text-muted"><?= e($f['contato_cargo']) ?></small><?php endif; ?>
                    </td>
                    <td><?= e($f['contato_telefone']) ?></td>
                    <td><?= e($f['estado']) ?></td>
                    <td><?= $f['created_at'] ? format_date(substr($f['created_at'], 0, 10)) : '—' ?></td>
                </tr>
                <!-- Linha de detalhes expansível -->
                <tr class="row-details" id="expand-<?= $f['id'] ?>" style="display:none">
                    <td colspan="6">
                        <div class="row-details-inner">
                            <p><strong>Endereço:</strong> <?= e($f['endereco'] ?? '—') ?>, <?= e($f['bairro'] ?? '') ?> — <?= e($f['cidade'] ?? '') ?>/<?= e($f['estado'] ?? '') ?> CEP: <?= e($f['cep'] ?? '') ?></p>
                            <p><strong>E-mail:</strong> <?= $f['contato_email'] ? '<a href="mailto:' . e($f['contato_email']) . '">' . e($f['contato_email']) . '</a>' : '—' ?></p>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pag['total_pages'] > 1): echo pagination_html($pag, base_url('/admin/fornecedores/listar.php') . '?busca=' . urlencode($busca)); endif; ?>
</div>

<script>
// Expandir linha ao clicar
document.querySelectorAll('.row-expandable').forEach(row => {
    row.style.cursor = 'pointer';
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
