<?php
/**
 * Editais e Documentos — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Editais e Documentos';
$page_description = 'Consulte os editais, homologações e documentos dos projetos esportivos do Instituto Atleta Para Sempre.';

// Filtro por projeto
$filtro_projeto = (int)($_GET['projeto'] ?? 0);

// Projetos ativos com status
$where = 'WHERE p.ativo = 1';
$params = [];
if ($filtro_projeto > 0) {
    $where .= ' AND p.id = ?';
    $params[] = $filtro_projeto;
}

$projetos = db_fetch_all(
    "SELECT p.*, ps.projetos_status as status_nome
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     $where
     ORDER BY p.nome_projeto",
    $params
);

// Todos projetos para o filtro
$todos_projetos = db_fetch_all(
    'SELECT id, nome_projeto FROM tab_projetos WHERE ativo = 1 ORDER BY nome_projeto'
);

$status_class = [
    'Concluído'   => 'badge-success',
    'Em Execução' => 'badge-primary',
    'Em Prestação de Contas' => 'badge-warning',
    'Assinado'    => 'badge-info',
];

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Editais</span>
        </nav>
        <h1 class="page-hero-title">Editais e Documentos</h1>
        <p class="page-hero-sub">Transparência total na gestão dos nossos projetos esportivos.</p>
    </div>
</section>

<section class="section" id="editais">
    <div class="container">

        <!-- FILTRO -->
        <?php if (!empty($todos_projetos)): ?>
        <div class="editais-filter fade-in-up">
            <form method="GET" action="/editais" id="form-filtro">
                <label for="projeto" class="form-label">Filtrar por projeto:</label>
                <div class="filter-row">
                    <select name="projeto" id="projeto" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos os projetos</option>
                        <?php foreach ($todos_projetos as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filtro_projeto === (int)$p['id'] ? 'selected' : '' ?>>
                            <?= e($p['nome_projeto']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($filtro_projeto): ?>
                    <a href="/editais" class="btn btn-ghost btn-sm">Limpar filtro</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- LISTA DE PROJETOS -->
        <?php if (empty($projetos)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">📄</div>
            <h3>Nenhum documento disponível</h3>
            <p>Não há editais publicados no momento.</p>
        </div>
        <?php else: ?>

        <div class="accordion-list fade-in-up">
            <?php foreach ($projetos as $projeto): ?>
            <?php
                // Grupos de documentos deste projeto
                $grupos = db_fetch_all(
                    'SELECT * FROM tab_projetos_grupo_doc WHERE id_projeto = ? ORDER BY posicao, id',
                    [$projeto['id']]
                );
                if (empty($grupos)) continue;
            ?>
            <div class="accordion-item projeto-accordion" id="projeto-<?= $projeto['id'] ?>">
                <button class="accordion-header" type="button"
                        aria-expanded="false"
                        aria-controls="docs-<?= $projeto['id'] ?>">
                    <div class="accordion-header-info">
                        <span class="accordion-title"><?= e($projeto['nome_projeto']) ?></span>
                        <?php if ($projeto['num_proposta']): ?>
                        <span class="accordion-sub">Proposta nº <?= e($projeto['num_proposta']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="accordion-header-meta">
                        <?php if ($projeto['status_nome']): ?>
                        <span class="badge <?= e($status_class[$projeto['status_nome']] ?? 'badge-secondary') ?>">
                            <?= e($projeto['status_nome']) ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($projeto['valor']): ?>
                        <span class="projeto-valor"><?= e($projeto['valor']) ?></span>
                        <?php endif; ?>
                        <svg class="accordion-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </button>

                <div class="accordion-body" id="docs-<?= $projeto['id'] ?>">
                    <?php foreach ($grupos as $grupo): ?>
                    <?php
                        $docs = db_fetch_all(
                            'SELECT d.*, t.tipo_documento
                             FROM tab_projetos_documentos d
                             LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id
                             WHERE d.id_grupo_documento = ?
                             ORDER BY d.id',
                            [$grupo['id']]
                        );
                        if (empty($docs)) continue;
                    ?>
                    <div class="doc-group">
                        <h4 class="doc-group-title"><?= e($grupo['nome_grupo']) ?></h4>
                        <ul class="doc-list">
                            <?php foreach ($docs as $doc): ?>
                            <li class="doc-item">
                                <span class="doc-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </span>
                                <span class="doc-nome"><?= e($doc['nome_documento']) ?></span>
                                <span class="badge <?= $doc['tipo_documento'] === 'Editais' ? 'badge-primary' : 'badge-success' ?> badge-sm">
                                    <?= e($doc['tipo_documento'] ?? '') ?>
                                </span>
                                <a href="/uploads/projetos/<?= (int)$doc['id'] ?>.pdf"
                                   class="btn btn-ghost btn-sm doc-download"
                                   target="_blank"
                                   rel="noopener"
                                   aria-label="Baixar <?= e($doc['nome_documento']) ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Baixar PDF
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
