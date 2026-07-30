<?php
/**
 * Admin: Painel de Transferências (Transparência)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Painel de Transferências';
$breadcrumb = [
    ['label' => 'Dashboard',     'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Transparência'],
    ['label' => 'Painel'],
];

$pasta = UPLOAD_PATH . '/transparencia/';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'upload';

    if ($action === 'upload') {
        if (empty($_FILES['arquivo']['name'])) { flash('error', 'Selecione um arquivo.'); redirect(base_url('/admin/transparencia/painel.php')); }
        $prefixo = date('Y_m_d') . '_';
        $up = upload_file($_FILES['arquivo'], $pasta, ['jpg', 'jpeg', 'png', 'xlsx', 'xls', 'pdf'], $prefixo);
        if (!$up['success']) { flash('error', 'Erro: ' . $up['error']); } else { flash('success', 'Arquivo enviado!'); }
    } elseif ($action === 'delete') {
        $arq = basename($_POST['arquivo'] ?? '');
        if ($arq && file_exists($pasta . $arq)) { @unlink($pasta . $arq); flash('success', 'Arquivo removido.'); }
    }
    redirect(base_url('/admin/transparencia/painel.php'));
}

// Listar arquivos
$arquivos = [];
foreach (scandir($pasta) as $arq) {
    if ($arq === '.' || $arq === '..') continue;
    $ext = strtolower(pathinfo($arq, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'xlsx', 'xls', 'pdf'])) continue;
    $arquivos[] = ['nome' => $arq, 'ext' => $ext, 'tamanho' => filesize($pasta . $arq), 'url' => '/uploads/transparencia/' . $arq];
}
rsort($arquivos);

ob_start();
?>
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Painel de Anexos & Imagens</h1>
        <p class="page-subtitle">Repositório de anexos gerais, planilhas e comprovantes para transparência pública</p>
    </div>
</div>

<!-- Abas de Navegação de Transparência -->
<div class="card mb-6" style="padding: 0.5rem 0.75rem;">
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a href="/admin/transparencia/financeiro.php" class="btn btn-secondary btn-sm">
            📄 Documentos Financeiros
        </a>
        <a href="/admin/transparencia/termos.php" class="btn btn-secondary btn-sm">
            📜 Termos de Colaboração
        </a>
        <a href="/admin/transparencia/painel.php" class="btn btn-primary btn-sm">
            📁 Painel de Anexos e Imagens (<?= count($arquivos) ?>)
        </a>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: 1fr 340px;">
    <!-- Lista de arquivos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Arquivos Publicados (<?= count($arquivos) ?>)</h3>
        </div>
        <?php if (empty($arquivos)): ?>
        <div class="card-body text-center text-muted py-8">
            Nenhum arquivo anexado até o momento.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Arquivo</th>
                        <th style="width:80px">Tipo</th>
                        <th style="width:90px">Tamanho</th>
                        <th style="width:140px; text-align:right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arquivos as $a): ?>
                    <tr>
                        <td>
                            <?php if (in_array($a['ext'], ['jpg','jpeg','png'])): ?>
                            <img src="<?= e($a['url']) ?>" alt="" style="height:32px; width:32px; object-fit:cover; border-radius:4px; vertical-align:middle; margin-right:.5rem;">
                            <?php endif; ?>
                            <strong><?= e($a['nome']) ?></strong>
                        </td>
                        <td><span class="badge badge-secondary"><?= strtoupper($a['ext']) ?></span></td>
                        <td><span class="text-muted text-sm"><?= round($a['tamanho'] / 1024) ?> KB</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:0.35rem; justify-content:flex-end;">
                                <a href="<?= e($a['url']) ?>" target="_blank" class="btn btn-outline btn-sm">Ver</a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Confirma a remoção deste arquivo?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="arquivo" value="<?= e($a['nome']) ?>">
                                    <button type="submit" class="btn btn-ghost text-danger btn-sm">Remover</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Upload -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📤 Enviar Anexo</h3>
        </div>
        <div class="card-body">
            <p class="text-muted text-sm mb-4">O arquivo receberá um carimbo de data automaticamente.</p>
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload">
                <div class="form-group">
                    <label for="arquivo_painel" class="form-label">Arquivo *</label>
                    <input type="file" id="arquivo_painel" name="arquivo" class="form-input" required
                           accept=".jpg,.jpeg,.png,.xlsx,.xls,.pdf">
                    <small class="text-muted text-sm" style="display:block; margin-top:0.35rem;">Formatos: JPG, PNG, XLSX, XLS, PDF</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <span>Enviar Arquivo</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
