<?php
/**
 * Transparência: Termos de Colaboração — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Termos de Colaboração';
$page_description = 'Termos de fomento, colaboração e parcerias celebradas pelo Instituto Atleta Para Sempre.';

$termos = db_fetch_all('SELECT * FROM tab_documentos_termo_colaboracao ORDER BY data_documento DESC');
$total_termos = count($termos);
$ultimo_termo = !empty($termos) ? format_date($termos[0]['data_documento']) : 'N/A';

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Termos de Colaboração</span>
        </nav>
        <h1 class="page-hero-title">Termos de Fomento & Colaboração</h1>
        <p class="page-hero-sub">Parcerias públicas, acordos institucionais e termos de fomento celebrados.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link active">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="termos">
    <div class="container">
        <!-- SUMMARY METRICS GRID -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">🤝</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_termos ?></span>
                    <span class="s-lbl">Termos Registrados</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📜</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Lei 13.019/2014</span>
                    <span class="s-lbl">Regime Jurídico MROSC</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🏛️</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Poder Público</span>
                    <span class="s-lbl">Parcerias Estratégicas</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🕒</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem; font-family:var(--font-mono);"><?= e($ultimo_termo) ?></span>
                    <span class="s-lbl">Último Instrumento</span>
                </div>
            </div>
        </div>

        <?php if (empty($termos)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">🤝</div>
            <h3>Nenhum termo publicado</h3>
            <p>Os termos de colaboração e fomento celebrados serão listados publicamente nesta seção.</p>
        </div>
        <?php else: ?>

        <!-- TERMOS CARDS GRID -->
        <div class="painel-grid fade-in-up">
            <?php foreach ($termos as $t): ?>
            <?php 
                $ext = strtolower(pathinfo($t['arquivo'], PATHINFO_EXTENSION));
                $caminho_arq = UPLOAD_PATH . '/transparencia/' . $t['arquivo'];
                $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
            ?>
            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box" style="color:var(--color-primary); background:var(--color-primary-alpha);">
                        🤝
                    </div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc"><?= strtoupper($ext ?: 'PDF') ?></span>
                        <span class="p-date"><?= format_date($t['data_documento']) ?></span>
                    </div>
                    <h3 class="painel-card-title"><?= e($t['titulo']) ?></h3>
                    <?php if (!empty($t['resumo'])): ?>
                        <p class="painel-card-desc"><?= e($t['resumo']) ?></p>
                    <?php else: ?>
                        <p class="painel-card-desc">Instrumento formal de parceria e termo de colaboração institucional.</p>
                    <?php endif; ?>
                    
                    <a href="/uploads/transparencia/<?= e($t['arquivo']) ?>" class="btn btn-primary btn-sm w-100" target="_blank" rel="noopener">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Baixar Termo de Fomento (<?= $tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF' ?>)
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
