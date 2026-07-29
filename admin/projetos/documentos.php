<?php
/**
 * Admin: Documentos do Projeto
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$projeto_id = (int)($_GET['projeto_id'] ?? 0);
if (!$projeto_id) { flash('error', 'Projeto não especificado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$projeto_id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$tipos_doc = db_fetch_all('SELECT * FROM tab_projetos_documentos_tipo ORDER BY id');

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    match ($action) {
        'add_group' => (function() use ($projeto_id) {
            $nome = trim($_POST['nome_grupo'] ?? '');
            $pos  = (int)($_POST['posicao'] ?? 0);
            if ($nome) {
                db_insert('tab_projetos_grupo_doc', ['id_projeto' => $projeto_id, 'nome_grupo' => $nome, 'posicao' => $pos]);
                flash('success', 'Grupo criado com sucesso!');
            }
        })(),

        'add_doc' => (function() use ($projeto_id) {
            $grupo_id = (int)($_POST['grupo_id'] ?? 0);
            $tipo_id  = (int)($_POST['tipo_id'] ?? 0);
            $nome_doc = trim($_POST['nome_documento'] ?? '');

            if (!$grupo_id || !$tipo_id || !$nome_doc) {
                flash('error', 'Preencha todos os campos do documento.'); return;
            }
            if (empty($_FILES['arquivo_doc']) || $_FILES['arquivo_doc']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'Selecione um arquivo PDF.'); return;
            }

            $upload = upload_file($_FILES['arquivo_doc'], UPLOAD_PATH . '/projetos', ['pdf']);
            if (!$upload['success']) { flash('error', 'Erro no upload: ' . $upload['error']); return; }

            db_insert('tab_projetos_documentos', [
                'id_projeto'         => $projeto_id,
                'id_grupo_documento' => $grupo_id,
                'id_tipo_documento'  => $tipo_id,
                'nome_documento'     => $nome_doc,
                'arquivo'            => $upload['filename'],
            ]);
            flash('success', 'Documento adicionado!');
        })(),

        'delete_doc' => (function() {
            $doc_id = (int)($_POST['doc_id'] ?? 0);
            if (!$doc_id) return;
            $doc = db_fetch('SELECT arquivo FROM tab_projetos_documentos WHERE id = ?', [$doc_id]);
            if ($doc) {
                @unlink(UPLOAD_PATH . '/projetos/' . $doc['arquivo']);
                db_delete('tab_projetos_documentos', 'id = ?', [$doc_id]);
                flash('success', 'Documento removido.');
            }
        })(),

        'delete_group' => (function() {
            $grupo_id = (int)($_POST['grupo_id'] ?? 0);
            if (!$grupo_id) return;
            $docs = db_fetch_all('SELECT arquivo FROM tab_projetos_documentos WHERE id_grupo_documento = ?', [$grupo_id]);
            foreach ($docs as $d) @unlink(UPLOAD_PATH . '/projetos/' . $d['arquivo']);
            db_delete('tab_projetos_documentos', 'id_grupo_documento = ?', [$grupo_id]);
            db_delete('tab_projetos_grupo_doc', 'id = ?', [$grupo_id]);
            flash('success', 'Grupo e documentos removidos.');
        })(),

        default => null,
    };

    redirect(base_url('/admin/projetos/documentos.php?projeto_id=' . $projeto_id));
}

$grupos = db_fetch_all(
    'SELECT * FROM tab_projetos_grupo_doc WHERE id_projeto = ? ORDER BY posicao, id',
    [$projeto_id]
);

$page_title = 'Admin — Documentos do Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Documentos: ' . truncate($projeto['nome_projeto'], 35)],
];

ob_start();
?>
<div class="page-section-header">
    <h2><?= e($projeto['nome_projeto']) ?></h2>
    <a href="<?= base_url('/admin/projetos/editar.php?id=' . $projeto_id) ?>" class="btn btn-outline btn-sm">← Voltar ao projeto</a>
</div>

<!-- GRUPOS EXISTENTES -->
<?php if (empty($grupos)): ?>
<div class="empty-state small"><p>Nenhum grupo de documentos cadastrado ainda.</p></div>
<?php else: ?>
<div class="groups-list">
    <?php foreach ($grupos as $grupo): ?>
    <?php $docs = db_fetch_all('SELECT d.*, t.tipo_documento FROM tab_projetos_documentos d LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id WHERE d.id_grupo_documento = ? ORDER BY d.id', [$grupo['id']]); ?>
    <div class="admin-card group-card">
        <div class="group-card-header">
            <h4><?= e($grupo['nome_grupo']) ?> <small class="text-muted">Posição: <?= (int)$grupo['posicao'] ?></small></h4>
            <form method="POST" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_group">
                <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Excluir grupo e TODOS os seus documentos?">Excluir Grupo</button>
            </form>
        </div>
        <?php if (empty($docs)): ?>
        <p class="text-muted text-sm">Sem documentos neste grupo.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Documento</th><th>Tipo</th><th>Arquivo</th><th>Ação</th></tr></thead>
            <tbody>
                <?php foreach ($docs as $doc): ?>
                <tr>
                    <td><?= e($doc['nome_documento']) ?></td>
                    <td><span class="badge <?= $doc['tipo_documento'] === 'Editais' ? 'badge-primary' : 'badge-success' ?>"><?= e($doc['tipo_documento'] ?? '') ?></span></td>
                    <td><a href="/uploads/projetos/<?= e($doc['arquivo']) ?>" target="_blank" class="btn btn-ghost btn-sm">Ver PDF</a></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete_doc">
                            <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remover este documento?">Remover</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- FORMULÁRIOS -->
<div class="admin-forms-grid">
    <!-- Adicionar Grupo -->
    <div class="admin-card">
        <h3 class="admin-card-title">➕ Novo Grupo de Documentos</h3>
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_group">
            <div class="form-group">
                <label for="nome_grupo" class="form-label">Nome do Grupo <span class="required">*</span></label>
                <input type="text" id="nome_grupo" name="nome_grupo" class="form-input" required placeholder="Ex: 001/2025 - Recursos Humanos">
            </div>
            <div class="form-group">
                <label for="posicao" class="form-label">Posição (ordem)</label>
                <input type="number" id="posicao" name="posicao" class="form-input" value="<?= count($grupos) + 1 ?>" min="1">
            </div>
            <button type="submit" class="btn btn-primary">Criar Grupo</button>
        </form>
    </div>

    <!-- Upload Documento -->
    <div class="admin-card">
        <h3 class="admin-card-title">📄 Adicionar Documento</h3>
        <?php if (empty($grupos)): ?>
        <p class="text-muted">Crie um grupo primeiro.</p>
        <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_doc">
            <div class="form-group">
                <label for="grupo_id" class="form-label">Grupo <span class="required">*</span></label>
                <select id="grupo_id" name="grupo_id" class="form-select" required>
                    <option value="">Selecione o grupo</option>
                    <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= e($g['nome_grupo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_id" class="form-label">Tipo <span class="required">*</span></label>
                <select id="tipo_id" name="tipo_id" class="form-select" required>
                    <option value="">Selecione o tipo</option>
                    <?php foreach ($tipos_doc as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= e($t['tipo_documento']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nome_documento" class="form-label">Nome do Documento <span class="required">*</span></label>
                <input type="text" id="nome_documento" name="nome_documento" class="form-input" required placeholder="Ex: Edital de Seleção nº 001/2025">
            </div>
            <div class="form-group">
                <label for="arquivo_doc" class="form-label">Arquivo PDF <span class="required">*</span></label>
                <input type="file" id="arquivo_doc" name="arquivo_doc" class="form-input" accept=".pdf" required data-accept="pdf">
            </div>
            <button type="submit" class="btn btn-primary">Adicionar Documento</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
