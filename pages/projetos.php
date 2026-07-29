<?php
/**
 * Projetos Esportivos — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Projetos Esportivos';
$page_description = 'Conheça todos os projetos e iniciativas esportivas incentivadas mantidas pelo Instituto Atleta Para Sempre.';

// Buscar todos os projetos ativos
$projetos = db_fetch_all(
    "SELECT p.*, ps.projetos_status as status_nome
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     ORDER BY p.id DESC"
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
            <span>Projetos Esportivos</span>
        </nav>
        <h1 class="page-hero-title"><i data-lucide="trophy" style="color:var(--color-primary);"></i> Projetos Esportivos</h1>
        <p class="page-hero-sub">Iniciativas de incentivo ao esporte, inclusão social e formação de atletas em todo o Brasil.</p>
    </div>
</section>

<section class="section" id="projetos-lista">
    <div class="container">

        <?php if (empty($projetos)): ?>
        <div class="card text-center fade-in-up" style="padding: 3rem;">
            <div style="width:56px; height:56px; margin:0 auto 1rem auto; border-radius:50%; background:var(--color-primary-alpha); color:var(--color-primary); display:flex; align-items:center; justify-content:center;">
                <i data-lucide="folder-x" style="width:28px; height:28px;"></i>
            </div>
            <h3>Nenhum projeto cadastrado</h3>
            <p style="color:var(--text-muted);">Não há projetos cadastrados no momento.</p>
        </div>
        <?php else: ?>

        <div class="projects-grid fade-in-up">
            <?php foreach ($projetos as $p): ?>
            <?php
                // Contar documentos deste projeto
                $total_docs = db_count('SELECT COUNT(*) FROM tab_projetos_documentos WHERE id_projeto = ?', [$p['id']]);
            ?>
            <article class="project-card fade-in-up">
                <div>
                    <div class="project-card-header">
                        <span class="badge <?= e($status_class[$p['status_nome']] ?? 'badge-secondary') ?>">
                            <i data-lucide="activity" style="width:12px; height:12px;"></i>
                            <?= e($p['status_nome'] ?? 'Em andamento') ?>
                        </span>
                        <?php if ($p['valor']): ?>
                        <span style="font-weight:800; font-family:var(--font-mono); color:var(--color-primary); font-size:0.85rem;">
                            <?= e($p['valor']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="project-card-title"><?= e($p['nome_projeto']) ?></h3>

                    <?php if ($p['num_proposta']): ?>
                    <p class="project-proposta"><i data-lucide="file-text"></i> Proposta Nº <?= e($p['num_proposta']) ?></p>
                    <?php endif; ?>

                    <?php if ($p['objeto']): ?>
                    <p class="project-card-desc"><?= e(truncate($p['objeto'], 160)) ?></p>
                    <?php endif; ?>
                </div>

                <div style="border-top:1px dashed var(--border-color); padding-top:1rem; margin-top:1rem; display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:0.75rem; color:var(--text-muted);">
                        <i data-lucide="files" style="width:14px; height:14px;"></i> <?= $total_docs ?> <?= $total_docs === 1 ? 'documento' : 'documentos' ?>
                    </span>
                    <a href="/editais?projeto=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                        <i data-lucide="external-link"></i> Ver Documentos
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
