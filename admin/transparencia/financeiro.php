<?php
/**
 * Admin: Documentos Financeiros (Transparência)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Documentos Financeiros';
$breadcrumb = [
    ['label' => 'Dashboard',     'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Transparência'],
    ['label' => 'Financeiro'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $data   = $_POST['data_documento'] ?? '';
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        if (!$titulo || !$data) { flash('error', 'Título e data são obrigatórios.'); redirect(base_url('/admin/transparencia/financeiro.php')); }
        if (empty($_FILES['arquivo']['name'])) { flash('error', 'Selecione um arquivo.'); redirect(base_url('/admin/transparencia/financeiro.php')); }
        $up = upload_file($_FILES['arquivo'], UPLOAD_PATH . '/transparencia', ['pdf']);
        if (!$up['success']) { flash('error', 'Erro no upload: ' . $up['error']); redirect(base_url('/admin/transparencia/financeiro.php')); }
        db_insert('tab_documentos_financeiro', ['data_documento' => $data, 'titulo' => $titulo, 'resumo' => $resumo, 'arquivo' => $up['filename']]);
        flash('success', 'Documento adicionado!');
    } elseif ($action === 'delete') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT arquivo FROM tab_documentos_financeiro WHERE id = ?', [$did]);
            if ($doc) { @unlink(UPLOAD_PATH . '/transparencia/' . $doc['arquivo']); db_delete('tab_documentos_financeiro', 'id = ?', [$did]); flash('success', 'Documento removido.'); }
        }
    }
    redirect(base_url('/admin/transparencia/financeiro.php'));
}

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro ORDER BY data_documento DESC');

ob_start();
?>
<div class="admin-forms-grid">
    <!-- Lista -->
    <div class="admin-card">
        <h3 class="admin-card-title">Documentos Cadastrados (<?= count($docs) ?>)</h3>
        <?php if (empty($docs)): ?>
        <p class="text-muted">Nenhum documento cadastrado.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Data</th><th>Título</th><th>Arquivo</th><th>Ação</th></tr></thead>
            <tbody>
                <?php foreach ($docs as $d): ?>
                <tr>
                    <td><?= format_date($d['data_documento']) ?></td>
                    <td>
                        <strong><?= e($d['titulo']) ?></strong>
                        <?php if ($d['resumo']): ?><br><small class="text-muted"><?= e(truncate($d['resumo'], 60)) ?></small><?php endif; ?>
                    </td>
                    <td><a href="/uploads/transparencia/<?= e($d['arquivo']) ?>" target="_blank" class="btn btn-ghost btn-sm">Ver</a></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Excluir este documento?">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Adicionar -->
    <div class="admin-card">
        <h3 class="admin-card-title">➕ Novo Documento</h3>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="data_documento" class="form-label">Data <span class="required">*</span></label>
                <input type="date" id="data_documento" name="data_documento" class="form-input" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label for="titulo" class="form-label">Título <span class="required">*</span></label>
                <input type="text" id="titulo" name="titulo" class="form-input" required placeholder="Ex: Relatório Financeiro 2024">
            </div>
            <div class="form-group">
                <label for="resumo" class="form-label">Resumo</label>
                <textarea id="resumo" name="resumo" class="form-textarea" rows="2" placeholder="Breve descrição..."></textarea>
            </div>
            <div class="form-group">
                <label for="arquivo_fin" class="form-label">Arquivo PDF <span class="required">*</span></label>
                <input type="file" id="arquivo_fin" name="arquivo" class="form-input" accept=".pdf" required data-accept="pdf">
            </div>
            <button type="submit" class="btn btn-primary">Adicionar Documento</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
