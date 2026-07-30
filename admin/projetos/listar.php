<?php
// admin/projetos/listar.php — Lista todos os projetos com ações
declare(strict_types=1);

require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

auth_require();

// ── Toggle ativo/inativo ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_ativo') {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $projeto = db_fetch('SELECT id, ativo FROM tab_projetos WHERE id = ?', [$id]);
        if ($projeto) {
            $novo_valor = $projeto['ativo'] ? 0 : 1;
            db_update('tab_projetos', ['ativo' => $novo_valor], 'id = ?', [$id]);
            $msg = $novo_valor ? 'Projeto ativado com sucesso.' : 'Projeto desativado com sucesso.';
            flash('success', $msg);
        }
    }
    redirect('/admin/projetos/listar.php');
}

// ── Status para Filtro ────────────────────────────────────────────────────────
$status_list = [];
try {
    $status_list = db_fetch_all('SELECT id, projetos_status FROM tab_projetos_status ORDER BY id ASC');
} catch (Throwable $e) {
    $status_list = [];
}

// ── Listagem com filtros ──────────────────────────────────────────────────────
$projetos = db_fetch_all(
    'SELECT p.*, ps.projetos_status AS nome_status
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     ORDER BY p.ativo DESC, p.nome_projeto ASC'
);

$page_title = 'Admin — Projetos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/admin/dashboard.php'],
    ['label' => 'Projetos', 'url' => '']
];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Projetos</h1>
        <p class="page-subtitle">Gerenciamento de projetos sociais e esportivos (<?= count($projetos) ?> cadastrados)</p>
    </div>
    <div style="display:flex; align-items:center; gap:0.625rem;">
        <!-- Alternador de Visualização Cards / Tabela -->
        <div class="view-toggle-btns">
            <button type="button" class="view-toggle-btn active" id="btn-view-grid" title="Visualização em Cards">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </button>
            <button type="button" class="view-toggle-btn" id="btn-view-table" title="Visualização em Tabela">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </button>
        </div>

        <a href="/admin/projetos/criar.php" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Novo Projeto</span>
        </a>
    </div>
</div>

<?php if ($msg_flash = flash('success')): ?>
    <div class="alert alert-success mb-4"><?= e($msg_flash) ?></div>
<?php endif; ?>
<?php if ($msg_flash = flash('error')): ?>
    <div class="alert alert-danger mb-4"><?= e($msg_flash) ?></div>
<?php endif; ?>

<!-- Barra de Filtros Interativa -->
<div class="card mb-4" style="padding: 1rem 1.25rem;">
    <div class="filter-bar">
        <input type="text" id="filtro-busca" class="form-input form-input-sm" placeholder="🔍 Pesquisar por nome, proposta ou termo..." style="min-width: 240px; flex-grow: 1; max-width: 400px;">
        
        <select id="filtro-status" class="form-select form-select-sm">
            <option value="">Todos os status</option>
            <?php foreach ($status_list as $st): ?>
                <option value="<?= e(strtolower($st['projetos_status'])) ?>"><?= e($st['projetos_status']) ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filtro-ativo" class="form-select form-select-sm">
            <option value="">Todos (Ativos / Inativos)</option>
            <option value="1">Somente Ativos</option>
            <option value="0">Somente Inativos</option>
        </select>

        <span class="filter-count" id="contador-projetos"><?= count($projetos) ?> projeto(s)</span>
    </div>
</div>

<!-- Visualização em Cards (Grid) -->
<div class="project-grid" id="container-grid">
    <?php if (empty($projetos)): ?>
        <div class="card p-6 text-center text-muted" style="grid-column: 1 / -1;">
            Nenhum projeto cadastrado até o momento.
        </div>
    <?php else: ?>
        <?php foreach ($projetos as $proj): ?>
            <div class="project-card item-projeto" 
                 data-nome="<?= e(strtolower($proj['nome_projeto'] . ' ' . ($proj['termo_fomento'] ?? '') . ' ' . ($proj['num_proposta'] ?? ''))) ?>"
                 data-status="<?= e(strtolower($proj['nome_status'] ?? '')) ?>"
                 data-ativo="<?= $proj['ativo'] ? '1' : '0' ?>">
                
                <div class="project-card-header">
                    <?php if (!empty($proj['nome_status'])): ?>
                        <span class="badge badge-info"><?= e($proj['nome_status']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Geral</span>
                    <?php endif; ?>

                    <?php if ($proj['ativo']): ?>
                        <span class="badge badge-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Inativo</span>
                    <?php endif; ?>
                </div>

                <div class="project-card-body">
                    <h3 class="project-title"><?= e($proj['nome_projeto']) ?></h3>
                    
                    <div class="project-meta-row">
                        <?php if (!empty($proj['num_proposta'])): ?>
                            <div class="project-meta-item" title="Número da Proposta">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <span>Prop: <?= e($proj['num_proposta']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($proj['termo_fomento'])): ?>
                            <div class="project-meta-item" title="Termo de Fomento">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                <span><?= e($proj['termo_fomento']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($proj['valor'])): ?>
                        <div class="project-value-badge">
                            <?= e($proj['valor']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="project-card-footer">
                    <div style="display:flex; gap:0.25rem;">
                        <a href="/admin/projetos/editar.php?id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary" title="Editar informações">Editar</a>
                        <a href="/admin/projetos/documentos.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary" title="Documentos">Docs</a>
                        <a href="/admin/projetos/funcoes.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary" title="Funções">Funções</a>
                    </div>

                    <form method="POST" action="/admin/projetos/listar.php" style="display: inline;" onsubmit="return confirm('Confirma a alteração de status deste projeto?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle_ativo">
                        <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                        <button type="submit" class="btn btn-sm <?= $proj['ativo'] ? 'btn-ghost text-danger' : 'btn-success' ?>">
                            <?= $proj['ativo'] ? 'Desativar' : 'Ativar' ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Visualização em Tabela (Modo Alternativo) -->
<div class="card" id="container-table" style="display:none;">
    <div class="table-responsive">
        <table class="data-table" id="tabela-projetos">
            <thead>
                <tr>
                    <th>Nome do Projeto</th>
                    <th>Nº Proposta</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ativo</th>
                    <th style="width: 220px; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projetos as $proj): ?>
                    <tr class="item-projeto-linha"
                        data-nome="<?= e(strtolower($proj['nome_projeto'] . ' ' . ($proj['termo_fomento'] ?? '') . ' ' . ($proj['num_proposta'] ?? ''))) ?>"
                        data-status="<?= e(strtolower($proj['nome_status'] ?? '')) ?>"
                        data-ativo="<?= $proj['ativo'] ? '1' : '0' ?>">
                        <td>
                            <strong><?= e($proj['nome_projeto']) ?></strong>
                            <?php if (!empty($proj['termo_fomento'])): ?>
                                <br><small class="text-muted"><?= e($proj['termo_fomento']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($proj['num_proposta'] ?? '—') ?></td>
                        <td><strong><?= !empty($proj['valor']) ? e($proj['valor']) : '—' ?></strong></td>
                        <td>
                            <?php if (!empty($proj['nome_status'])): ?>
                                <span class="badge badge-info"><?= e($proj['nome_status']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($proj['ativo']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                <a href="/admin/projetos/editar.php?id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="/admin/projetos/documentos.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Docs</a>
                                <a href="/admin/projetos/funcoes.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Funções</a>
                                <form method="POST" action="/admin/projetos/listar.php" style="display: inline;" onsubmit="return confirm('Confirma a alteração?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_ativo">
                                    <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $proj['ativo'] ? 'btn-ghost text-danger' : 'btn-success' ?>">
                                        <?= $proj['ativo'] ? 'Desativar' : 'Ativar' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputBusca  = document.getElementById('filtro-busca');
    const selectStatus = document.getElementById('filtro-status');
    const selectAtivo  = document.getElementById('filtro-ativo');
    const contador     = document.getElementById('contador-projetos');

    const btnGrid  = document.getElementById('btn-view-grid');
    const btnTable = document.getElementById('btn-view-table');
    const gridView = document.getElementById('container-grid');
    const tableView = document.getElementById('container-table');

    // Alternar modos de visualização
    btnGrid?.addEventListener('click', () => {
        btnGrid.classList.add('active');
        btnTable.classList.remove('active');
        gridView.style.display = 'grid';
        tableView.style.display = 'none';
    });

    btnTable?.addEventListener('click', () => {
        btnTable.classList.add('active');
        btnGrid.classList.remove('active');
        gridView.style.display = 'none';
        tableView.style.display = 'block';
    });

    // Função de Filtragem Interativa
    function aplicarFiltros() {
        const busca  = inputBusca.value.toLowerCase().trim();
        const status = selectStatus.value.toLowerCase().trim();
        const ativo  = selectAtivo.value.trim();

        const cards  = document.querySelectorAll('.item-projeto');
        const linhas = document.querySelectorAll('.item-projeto-linha');

        let visiveis = 0;

        cards.forEach((card, idx) => {
            const cardNome   = card.dataset.nome || '';
            const cardStatus = card.dataset.status || '';
            const cardAtivo  = card.dataset.ativo || '';

            const matchBusca  = !busca  || cardNome.includes(busca);
            const matchStatus = !status || cardStatus.includes(status);
            const matchAtivo  = !ativo  || cardAtivo === ativo;

            const visivel = matchBusca && matchStatus && matchAtivo;

            card.style.display = visivel ? 'flex' : 'none';
            if (linhas[idx]) linhas[idx].style.display = visivel ? 'table-row' : 'none';

            if (visivel) visiveis++;
        });

        if (contador) contador.innerText = `${visiveis} projeto(s)`;
    }

    inputBusca?.addEventListener('input', aplicarFiltros);
    selectStatus?.addEventListener('change', aplicarFiltros);
    selectAtivo?.addEventListener('change', aplicarFiltros);
});
</script>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
