<?php
/**
 * Admin: Termos de Colaboração (Transparência)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Termos de Colaboração';
$breadcrumb = [
    ['label' => 'Dashboard',     'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Transparência'],
    ['label' => 'Termos'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $data   = $_POST['data_documento'] ?? '';
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        if (!$titulo || !$data) { flash('error', 'Título e data são obrigatórios.'); redirect(base_url('/admin/transparencia/termos.php')); }
        if (empty($_FILES['arquivo']['name'])) { flash('error', 'Selecione um arquivo.'); redirect(base_url('/admin/transparencia/termos.php')); }
        $up = upload_file($_FILES['arquivo'], UPLOAD_PATH . '/transparencia', ['pdf']);
        if (!$up['success']) { flash('error', 'Erro: ' . $up['error']); redirect(base_url('/admin/transparencia/termos.php')); }
        db_insert('tab_documentos_termo_colaboracao', ['data_documento' => $data, 'titulo' => $titulo, 'resumo' => $resumo, 'arquivo' => $up['filename']]);
        flash('success', 'Termo adicionado!');
    } elseif ($action === 'delete') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT arquivo FROM tab_documentos_termo_colaboracao WHERE id = ?', [$did]);
            if ($doc) { @unlink(UPLOAD_PATH . '/transparencia/' . $doc['arquivo']); db_delete('tab_documentos_termo_colaboracao', 'id = ?', [$did]); flash('success', 'Termo removido.'); }
        }
    }
    redirect(base_url('/admin/transparencia/termos.php'));
}

$termos = db_fetch_all('SELECT * FROM tab_documentos_termo_colaboracao ORDER BY data_documento DESC');

ob_start();
?>
<div class="admin-forms-grid">
    <div class="admin-card">
        <h3 class="admin-card-title">Termos Cadastrados (<?= count($termos) ?>)</h3>
        <?php if (empty($termos)): ?>
        <p class="text-muted">Nenhum termo cadastrado.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Data</th><th>Título</th><th>Arquivo</th><th>Ação</th></tr></thead>
            <tbody>
                <?php foreach ($termos as $t): ?>
                <tr>
                    <td><?= format_date($t['data_documento']) ?></td>
                    <td><strong><?= e($t['titulo']) ?></strong><?php if ($t['resumo']): ?><br><small class="text-muted"><?= e(truncate($t['resumo'], 60)) ?></small><?php endif; ?></td>
                    <td><a href="/uploads/transparencia/<?= e($t['arquivo']) ?>" target="_blank" class="btn btn-ghost btn-sm">Ver</a></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="doc_id" value="<?= $t['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Excluir este termo?">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h3 class="admin-card-title">➕ Novo Termo</h3>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="data_documento" class="form-label">Data <span class="required">*</span></label>
                <input type="date" id="data_documento" name="data_documento" class="form-input" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label for="titulo" class="form-label">Título <span class="required">*</span></label>
                <input type="text" id="titulo" name="titulo" class="form-input" required placeholder="Ex: Termo de Fomento nº 001/2024">
            </div>
            <div class="form-group">
                <label for="resumo" class="form-label">Resumo</label>
                <textarea id="resumo" name="resumo" class="form-textarea" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="arquivo_termo" class="form-label">Arquivo PDF <span class="required">*</span></label>
                <input type="file" id="arquivo_termo" name="arquivo" class="form-input" accept=".pdf" required data-accept="pdf">
            </div>
            <button type="submit" class="btn btn-primary">Adicionar Termo</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
