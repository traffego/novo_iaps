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
<!-- FILTROS -->
<div class="admin-card" style="margin-bottom:1rem">
    <form method="GET" id="form-filtro" class="filter-bar">
        <select name="funcao_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Todas as funções</option>
            <?php foreach ($funcoes as $f): ?>
            <option value="<?= $f['id'] ?>" <?= $filtro_funcao === (int)$f['id'] ? 'selected' : '' ?>><?= e($f['funcao']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="projeto_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Todos os projetos</option>
            <?php foreach ($projetos as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $filtro_projeto === (int)$p['id'] ? 'selected' : '' ?>><?= e(truncate($p['nome_projeto'], 50)) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($filtro_funcao || $filtro_projeto): ?>
        <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-ghost btn-sm">Limpar</a>
        <?php endif; ?>
        <span class="filter-count"><?= $total ?> resultado(s)</span>
    </form>
</div>

<div class="admin-card">
    <input type="text" id="busca-tabela" class="form-input" placeholder="🔍 Pesquisar..." style="max-width:340px;margin-bottom:1rem">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Função</th>
                    <th>Projeto</th>
                    <th>Data</th>
                    <th>CV</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($curriculos)): ?>
                <tr><td colspan="7" class="text-center text-muted">Nenhum currículo encontrado.</td></tr>
                <?php else: ?>
                <?php foreach ($curriculos as $c): ?>
                <tr>
                    <td><?= (int)$c['id'] ?></td>
                    <td><strong><?= e($c['nome']) ?></strong><br><small class="text-muted"><?= e($c['e_mail'] ?? '') ?></small></td>
                    <td><?= e($c['telefone_1']) ?></td>
                    <td><?= e($c['funcao'] ?? '—') ?></td>
                    <td><?= e(truncate($c['nome_projeto'] ?? '—', 40)) ?></td>
                    <td><?= $c['created_at'] ? format_date(substr($c['created_at'], 0, 10)) : '—' ?></td>
                    <td>
                        <?php $pdf_path = UPLOAD_PATH . '/curriculos/' . $c['arquivo_curriculo']; ?>
                        <?php if (!empty($c['arquivo_curriculo']) && file_exists($pdf_path)): ?>
                        <a href="/uploads/curriculos/<?= e($c['arquivo_curriculo']) ?>" target="_blank" class="btn btn-outline btn-sm">📄 PDF</a>
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
    <?php if ($pag['total_pages'] > 1): echo pagination_html($pag, base_url('/admin/curriculos/listar.php')); endif; ?>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
