<?php
/**
 * Transparência: Painel de Transferências — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Painel de Transferências';
$page_description = 'Painel de transferências legais e discricionárias do Instituto Atleta Para Sempre.';

// Escanear pasta de transparência
$pasta = UPLOAD_PATH . '/transparencia/';
$arquivos = [];
if (is_dir($pasta)) {
    $scan = scandir($pasta);
    foreach ($scan as $arq) {
        if ($arq === '.' || $arq === '..') continue;
        $ext = strtolower(pathinfo($arq, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'xlsx', 'xls', 'pdf'])) continue;
        
        $caminho_completo = $pasta . $arq;
        $tamanho_bytes = file_exists($caminho_completo) ? filesize($caminho_completo) : 0;
        $data_modificacao = file_exists($caminho_completo) ? filemtime($caminho_completo) : time();
        
        // Extrair data do prefixo do nome (YYYY_MM_DD) se houver
        $data_exibicao = date('d/m/Y', $data_modificacao);
        if (preg_match('/^(\d{4})_(\d{2})_(\d{2})_/', $arq, $matches)) {
            $data_exibicao = "{$matches[3]}/{$matches[2]}/{$matches[1]}";
        }

        $arquivos[] = [
            'nome'         => $arq,
            'titulo'       => str_replace(['_', '-'], ' ', pathinfo($arq, PATHINFO_FILENAME)),
            'ext'          => $ext,
            'path'         => '/uploads/transparencia/' . $arq,
            'tamanho'      => round($tamanho_bytes / 1024, 1), // KB
            'data'         => $data_exibicao,
            'timestamp'    => $data_modificacao,
            'eh_imagem'    => in_array($ext, ['jpg', 'jpeg', 'png']),
        ];
    }
    usort($arquivos, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
}

// Estatísticas do Painel
$total_documentos = count($arquivos);
$total_imagens = count(array_filter($arquivos, fn($a) => $a['eh_imagem']));
$total_planilhas = count(array_filter($arquivos, fn($a) => !$a['eh_imagem']));
$ultima_atualizacao = !empty($arquivos) ? $arquivos[0]['data'] : 'N/A';

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Painel de Transferências</span>
        </nav>
        <h1 class="page-hero-title">Painel de Transferências</h1>
        <p class="page-hero-sub">Prestação de contas das transferências legais, discricionárias e recursos públicos recebidos.</p>
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
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link active">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="painel">
    <div class="container">
        <!-- ESTATÍSTICAS E RESUMO DO PAINEL -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">📊</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_documentos ?></span>
                    <span class="s-lbl">Transferências Publicadas</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📁</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_planilhas ?></span>
                    <span class="s-lbl">Planilhas & Relatórios</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📸</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_imagens ?></span>
                    <span class="s-lbl">Comprovantes & Painéis</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🕒</div>
                <div class="s-info">
                    <span class="s-num" style="font-size: 1.25rem; font-family: var(--font-mono);"><?= e($ultima_atualizacao) ?></span>
                    <span class="s-lbl">Última Atualização</span>
                </div>
            </div>
        </div>

        <?php if (empty($arquivos)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">📊</div>
            <h3>Painel em atualização</h3>
            <p>Os demonstrativos e planilhas de transferências serão publicados conforme a liberação dos relatórios periódicos.</p>
        </div>
        <?php else: ?>

        <!-- GRID DE DOCUMENTOS E PAINÉIS -->
        <div class="painel-grid fade-in-up">
            <?php foreach ($arquivos as $arq): ?>
            <div class="painel-card">
                <?php if ($arq['eh_imagem']): ?>
                <div class="painel-card-media">
                    <a href="<?= e($arq['path']) ?>" class="glightbox" data-title="Painel de Transferências — <?= e($arq['data']) ?>" data-description="Instituto Atleta Para Sempre">
                        <img src="<?= e($arq['path']) ?>" alt="Transferência <?= e($arq['data']) ?>" loading="lazy" class="painel-img">
                        <div class="painel-media-badge">
                            <span>🔍 Ver Painel Ampliado</span>
                        </div>
                    </a>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img"><?= strtoupper($arq['ext']) ?></span>
                        <span class="p-date"><?= e($arq['data']) ?></span>
                    </div>
                    <h3 class="painel-card-title">Demonstrativo Visual de Transferência</h3>
                    <p class="painel-card-desc"><?= e($arq['nome']) ?> (<?= $arq['tamanho'] ?> KB)</p>
                    <a href="<?= e($arq['path']) ?>" class="btn btn-outline btn-sm w-100 glightbox" data-title="Painel de Transferências — <?= e($arq['data']) ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        Ampliar Demonstrativo
                    </a>
                </div>
                <?php else: ?>
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc"><?= strtoupper($arq['ext']) ?></span>
                        <span class="p-date"><?= e($arq['data']) ?></span>
                    </div>
                    <h3 class="painel-card-title">Planilha & Prestação de Transferência</h3>
                    <p class="painel-card-desc"><?= e($arq['nome']) ?> (<?= $arq['tamanho'] ?> KB)</p>
                    <a href="<?= e($arq['path']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm w-100">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Baixar Arquivo Oficial
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
