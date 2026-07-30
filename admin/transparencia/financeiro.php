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
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Documentos Financeiros</h1>
        <p class="page-subtitle">Gestão de balanços, relatórios contábeis e demonstrações financeiras em PDF</p>
    </div>
</div>

<!-- Abas de Navegação de Transparência -->
<div class="card mb-6" style="padding: 0.5rem 0.75rem;">
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a href="/admin/transparencia/financeiro.php" class="btn btn-primary btn-sm">
            📄 Documentos Financeiros (<?= count($docs) ?>)
        </a>
        <a href="/admin/transparencia/termos.php" class="btn btn-secondary btn-sm">
            📜 Termos de Colaboração
        </a>
        <a href="/admin/transparencia/painel.php" class="btn btn-secondary btn-sm">
            📁 Painel de Anexos e Imagens
        </a>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: 1fr 340px;">
    <!-- Lista -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Documentos Financeiros Cadastrados (<?= count($docs) ?>)</h3>
        </div>
        <?php if (empty($docs)): ?>
        <div class="card-body text-center text-muted py-8">
            Nenhum documento financeiro cadastrado ainda.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:110px">Data</th>
                        <th>Título / Resumo</th>
                        <th style="width:80px">Arquivo</th>
                        <th style="width:80px; text-align:right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($docs as $d): ?>
                    <tr>
                        <td><span class="text-muted text-sm"><?= format_date($d['data_documento']) ?></span></td>
                        <td>
                            <strong><?= e($d['titulo']) ?></strong>
                            <?php if ($d['resumo']): ?><br><small class="text-muted"><?= e(truncate($d['resumo'], 70)) ?></small><?php endif; ?>
                        </td>
                        <td>
                            <a href="/uploads/transparencia/<?= e($d['arquivo']) ?>" target="_blank" class="btn btn-outline btn-sm" title="Abrir PDF">
                                📄 PDF
                            </a>
                        </td>
                        <td style="text-align:right;">
                            <form method="POST" style="display:inline" onsubmit="return confirm('Confirma a exclusão deste documento?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                                <button type="submit" class="btn btn-ghost text-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Adicionar -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">➕ Adicionar Documento</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="data_documento" class="form-label">Data do Documento *</label>
                    <input type="date" id="data_documento" name="data_documento" class="form-input" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="titulo" class="form-label">Título do Documento *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input" required placeholder="Ex: Balanço Patrimonial 2024">
                </div>

                <div class="form-group">
                    <label for="resumo" class="form-label">Resumo / Descrição</label>
                    <textarea id="resumo" name="resumo" class="form-textarea" rows="3" placeholder="Descrição opcional..."></textarea>
                </div>

                <div class="form-group">
                    <label for="arquivo_fin" class="form-label">Arquivo PDF *</label>
                    <input type="file" id="arquivo_fin" name="arquivo" class="form-input" accept=".pdf" required>
                    <small class="text-muted text-sm">Formato aceito: .PDF</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <span>Salvar Documento</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
