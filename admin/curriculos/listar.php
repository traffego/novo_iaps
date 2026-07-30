<?php
/**
 * Admin: Listar Currículos
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Currículos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos'],
];

// Filtros
$filtro_funcao   = (int)($_GET['funcao_id'] ?? 0);
$filtro_projeto  = (int)($_GET['projeto_id'] ?? 0);

$where  = '1=1';
$params = [];
if ($filtro_funcao)  { $where .= ' AND c.id_funcao = ?';  $params[] = $filtro_funcao; }
if ($filtro_projeto) { $where .= ' AND c.id_projeto = ?'; $params[] = $filtro_projeto; }

$por_pagina = 25;
$total = db_count("SELECT COUNT(*) FROM tab_curriculos c WHERE $where", $params);
$pag   = paginate($total, $por_pagina);

$curriculos = db_fetch_all(
    "SELECT c.*, f.funcao, p.nome_projeto
     FROM tab_curriculos c
     LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
     LEFT JOIN tab_projetos p ON c.id_projeto = p.id
     WHERE $where
     ORDER BY c.created_at DESC
     LIMIT ? OFFSET ?",
    [...$params, $por_pagina, $pag['offset']]
);

$funcoes  = db_fetch_all('SELECT * FROM tab_curriculos_funcao ORDER BY funcao');
$projetos = db_fetch_all('SELECT id, nome_projeto FROM tab_projetos ORDER BY nome_projeto');

ob_start();
?>
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Currículos</h1>
        <p class="page-subtitle">Gerenciamento de candidatos e banco de talentos (<?= $total ?> registros)</p>
    </div>
</div>

<!-- FILTROS -->
<div class="card mb-4" style="padding: 1rem 1.25rem;">
    <form method="GET" id="form-filtro" class="filter-bar">
        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; width:100%;">
            <input type="text" id="busca-tabela" class="form-input form-input-sm" placeholder="🔍 Filtrar nesta página..." style="min-width:220px;">
            
            <select name="funcao_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todas as funções</option>
                <?php foreach ($funcoes as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $filtro_funcao === (int)$f['id'] ? 'selected' : '' ?>><?= e($f['funcao']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="projeto_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos os projetos</option>
                <?php foreach ($projetos as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $filtro_projeto === (int)$p['id'] ? 'selected' : '' ?>><?= e(truncate($p['nome_projeto'], 45)) ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($filtro_funcao || $filtro_projeto): ?>
            <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-ghost btn-sm">Limpar Filtros</a>
            <?php endif; ?>

            <span class="filter-count"><?= $total ?> resultado(s) encontrado(s)</span>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table" id="tabela-curriculos">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Candidato / E-mail</th>
                    <th>Telefone</th>
                    <th>Função</th>
                    <th>Projeto</th>
                    <th>Data</th>
                    <th style="width:90px; text-align:center;">Currículo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($curriculos)): ?>
                <tr><td colspan="7" class="text-center text-muted py-8">Nenhum currículo encontrado.</td></tr>
                <?php else: ?>
                <?php foreach ($curriculos as $c): ?>
                <tr>
                    <td><span class="text-muted text-sm">#<?= (int)$c['id'] ?></span></td>
                    <td>
                        <strong><?= e($c['nome']) ?></strong>
                        <?php if (!empty($c['e_mail'])): ?>
                        <br><small class="text-muted"><?= e($c['e_mail']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= e($c['telefone_1'] ?: '—') ?></td>
                    <td><span class="badge badge-info"><?= e($c['funcao'] ?? 'Geral') ?></span></td>
                    <td><?= e(truncate($c['nome_projeto'] ?? '—', 38)) ?></td>
                    <td><span class="text-muted text-sm"><?= $c['created_at'] ? format_date(substr($c['created_at'], 0, 10)) : '—' ?></span></td>
                    <td style="text-align:center;">
                        <?php $arq = !empty($c['arquivo_curriculo']) ? $c['arquivo_curriculo'] : ($c['id'] . '.pdf'); ?>
                        <?php if (!empty($arq)): ?>
                        <a href="/uploads/curriculos/<?= e($arq) ?>" target="_blank" class="btn btn-outline btn-sm" title="Abrir PDF do Currículo">
                            📄 PDF
                        </a>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pag['total_pages'] > 1): ?>
        <div style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--adm-border);">
            <?= pagination_html($pag, base_url('/admin/curriculos/listar.php')) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('busca-tabela')?.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabela-curriculos tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
