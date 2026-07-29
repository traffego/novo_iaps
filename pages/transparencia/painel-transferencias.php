<?php
/**
 * Transparência: Painel de Transferências — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
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
        $arquivos[] = [
            'nome' => $arq,
            'ext'  => $ext,
            'path' => '/uploads/transparencia/' . $arq,
            'data' => substr($arq, 0, 10), // YYYY_MM_DD
        ];
    }
    usort($arquivos, fn($a, $b) => strcmp($b['nome'], $a['nome']));
}

// Agrupar por data
$grupos = [];
foreach ($arquivos as $arq) {
    $data_key = str_replace('_', '/', $arq['data']);
    $grupos[$data_key][] = $arq;
}

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span>›</span><a href="/transparencia/declaracao">Transparência</a><span>›</span><span>Painel de Transferências</span>
        </nav>
        <h1 class="page-hero-title">Painel de Transferências</h1>
        <p class="page-hero-sub">Transferências legais e discricionárias — atualizado periodicamente.</p>
    </div>
</section>

<section class="section" id="painel">
    <div class="container">
        <?php if (empty($arquivos)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">📊</div>
            <h3>Painel em atualização</h3>
            <p>Os dados serão publicados em breve.</p>
        </div>
        <?php else: ?>
        <div class="painel-grupos fade-in-up">
            <?php foreach ($grupos as $data => $itens): ?>
            <div class="painel-grupo">
                <h3 class="painel-grupo-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Atualização: <?= e($data) ?>
                </h3>
                <div class="painel-items">
                    <?php foreach ($itens as $arq): ?>
                    <?php $eh_imagem = in_array($arq['ext'], ['jpg', 'jpeg', 'png']); ?>
                    <?php if ($eh_imagem): ?>
                    <div class="painel-imagem-wrap">
                        <a href="<?= e($arq['path']) ?>" class="glightbox"
                           data-title="Painel de Transferências — <?= e($data) ?>"
                           data-description="Instituto Atleta Para Sempre">
                            <img src="<?= e($arq['path']) ?>"
                                 alt="Painel de transferências de <?= e($data) ?>"
                                 class="painel-imagem"
                                 loading="lazy">
                            <div class="painel-imagem-overlay">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Ampliar
                            </div>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="painel-download-wrap">
                        <a href="<?= e($arq['path']) ?>" class="download-card" target="_blank" rel="noopener">
                            <span class="download-ext"><?= strtoupper($arq['ext']) ?></span>
                            <div>
                                <strong>Planilha de Transferências</strong>
                                <span><?= e($data) ?></span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                    </div>
                    <?php endif; ?>
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
