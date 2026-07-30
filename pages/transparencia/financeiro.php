<?php
/**
 * Transparência: Financeiro — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Documentos Financeiros';
$page_description = 'Documentos financeiros, balanços contábeis e prestações de contas do Instituto Atleta Para Sempre.';

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro ORDER BY data_documento DESC');
$total_docs = count($docs);
$ultimo_doc = !empty($docs) ? format_date($docs[0]['data_documento']) : 'N/A';

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Documentos Financeiros</span>
        </nav>
        <h1 class="page-hero-title">Documentos Financeiros & Balanços</h1>
        <p class="page-hero-sub">Demostrativos contábeis, balanços patrimoniais e prestações de contas oficiais.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link active">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="financeiro">
    <div class="container">
        <!-- SUMMARY METRICS GRID -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">💰</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_docs ?></span>
                    <span class="s-lbl">Relatórios Publicados</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📊</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Balanço Patrimonial</span>
                    <span class="s-lbl">Demonstrativos Anuais</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🔍</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Contabilidade</span>
                    <span class="s-lbl">Auditoria & Prestação</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🕒</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem; font-family:var(--font-mono);"><?= e($ultimo_doc) ?></span>
                    <span class="s-lbl">Último Balanço</span>
                </div>
            </div>
        </div>

        <?php if (empty($docs)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">💰</div>
            <h3>Nenhum documento financeiro publicado</h3>
            <p>Os demonstrativos contábeis e relatórios financeiros são validados e publicados conforme as prestações de contas forem concluídas.</p>
        </div>
        <?php else: ?>

        <!-- FINANCIAL DOCUMENTS GRID -->
        <div class="painel-grid fade-in-up">
            <?php foreach ($docs as $doc): ?>
            <?php 
                $ext = strtolower(pathinfo($doc['arquivo'], PATHINFO_EXTENSION));
                $caminho_arq = UPLOAD_PATH . '/transparencia/' . $doc['arquivo'];
                $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
            ?>
            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box" style="color:var(--color-success); background:rgba(5,150,105,0.12);">
                        💰
                    </div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc"><?= strtoupper($ext ?: 'PDF') ?></span>
                        <span class="p-date"><?= format_date($doc['data_documento']) ?></span>
                    </div>
                    <h3 class="painel-card-title"><?= e($doc['titulo']) ?></h3>
                    <?php if (!empty($doc['resumo'])): ?>
                        <p class="painel-card-desc"><?= e($doc['resumo']) ?></p>
                    <?php else: ?>
                        <p class="painel-card-desc">Prestação de contas contábil e balanço financeiro institucional.</p>
                    <?php endif; ?>
                    
                    <a href="/uploads/transparencia/<?= e($doc['arquivo']) ?>" class="btn btn-primary btn-sm w-100" target="_blank" rel="noopener">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Baixar Balanço / Relatório (<?= $tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF' ?>)
                    </a>
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
