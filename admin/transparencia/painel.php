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
<div class="admin-forms-grid">
    <!-- Lista de arquivos -->
    <div class="admin-card">
        <h3 class="admin-card-title">Arquivos Publicados (<?= count($arquivos) ?>)</h3>
        <?php if (empty($arquivos)): ?>
        <p class="text-muted">Nenhum arquivo publicado.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Arquivo</th><th>Tipo</th><th>Tamanho</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($arquivos as $a): ?>
                <tr>
                    <td>
                        <?php if (in_array($a['ext'], ['jpg','jpeg','png'])): ?>
                        <img src="<?= e($a['url']) ?>" alt="" style="height:40px;border-radius:4px;vertical-align:middle;margin-right:.5rem">
                        <?php endif; ?>
                        <?= e($a['nome']) ?>
                    </td>
                    <td><span class="badge badge-secondary"><?= strtoupper($a['ext']) ?></span></td>
                    <td><?= round($a['tamanho'] / 1024) ?> KB</td>
                    <td class="actions">
                        <a href="<?= e($a['url']) ?>" target="_blank" class="btn btn-ghost btn-sm">Ver</a>
                        <form method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="arquivo" value="<?= e($a['nome']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remover este arquivo?">Remover</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Upload -->
    <div class="admin-card">
        <h3 class="admin-card-title">📤 Enviar Arquivo</h3>
        <p class="text-muted text-sm">O arquivo será renomeado automaticamente com a data de hoje no início do nome.</p>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="upload">
            <div class="form-group">
                <label for="arquivo_painel" class="form-label">Arquivo <span class="required">*</span></label>
                <input type="file" id="arquivo_painel" name="arquivo" class="form-input" required
                       accept=".jpg,.jpeg,.png,.xlsx,.xls,.pdf"
                       data-accept="jpg,jpeg,png,xlsx,xls,pdf">
                <small class="form-hint">Formatos aceitos: JPG, PNG, XLSX, XLS, PDF</small>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
