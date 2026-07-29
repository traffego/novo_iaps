<?php
/**
 * Admin: Funções/Cargos (CRUD)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Funções/Cargos';
$breadcrumb = [
    ['label' => 'Dashboard',  'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos', 'url' => base_url('/admin/curriculos/listar.php')],
    ['label' => 'Funções'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'criar') {
        $funcao = trim($_POST['funcao'] ?? '');
        if ($funcao) { db_insert('tab_curriculos_funcao', ['funcao' => $funcao, 'ativo' => 1]); flash('success', 'Função criada!'); }
    } elseif ($action === 'editar') {
        $fid    = (int)($_POST['id'] ?? 0);
        $funcao = trim($_POST['funcao'] ?? '');
        if ($fid && $funcao) { db_update('tab_curriculos_funcao', ['funcao' => $funcao], 'id = ?', [$fid]); flash('success', 'Função atualizada!'); }
    } elseif ($action === 'toggle') {
        $fid = (int)($_POST['id'] ?? 0);
        if ($fid) {
            $f = db_fetch('SELECT ativo FROM tab_curriculos_funcao WHERE id = ?', [$fid]);
            if ($f) db_update('tab_curriculos_funcao', ['ativo' => $f['ativo'] ? 0 : 1], 'id = ?', [$fid]);
            flash('success', 'Status alterado!');
        }
    }
    redirect(base_url('/admin/curriculos/funcoes.php'));
}

$funcoes = db_fetch_all('SELECT * FROM tab_curriculos_funcao ORDER BY funcao');
$editar_id = (int)($_GET['editar'] ?? 0);
$editar_funcao = $editar_id ? db_fetch('SELECT * FROM tab_curriculos_funcao WHERE id = ?', [$editar_id]) : null;

ob_start();
?>
<div class="admin-forms-grid">
    <!-- Lista -->
    <div class="admin-card">
        <h3 class="admin-card-title">Funções Cadastradas (<?= count($funcoes) ?>)</h3>
        <table class="data-table">
            <thead><tr><th>Função</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($funcoes as $f): ?>
                <tr>
                    <td><?= e($f['funcao']) ?></td>
                    <td><span class="badge <?= $f['ativo'] ? 'badge-success' : 'badge-secondary' ?>"><?= $f['ativo'] ? 'Ativa' : 'Inativa' ?></span></td>
                    <td class="actions">
                        <a href="?editar=<?= $f['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                        <form method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn btn-ghost btn-sm"><?= $f['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Form criar/editar -->
    <div class="admin-card">
        <h3 class="admin-card-title"><?= $editar_funcao ? 'Editar Função' : 'Nova Função' ?></h3>
        <form method="POST">
            <?= csrf_field() ?>
            <?php if ($editar_funcao): ?>
            <input type="hidden" name="action" value="editar">
            <input type="hidden" name="id" value="<?= $editar_funcao['id'] ?>">
            <?php else: ?>
            <input type="hidden" name="action" value="criar">
            <?php endif; ?>
            <div class="form-group">
                <label for="funcao" class="form-label">Nome da Função <span class="required">*</span></label>
                <input type="text" id="funcao" name="funcao" class="form-input" required
                       value="<?= e($editar_funcao['funcao'] ?? '') ?>" placeholder="Ex: Coordenador de Evento">
            </div>
            <div class="form-actions">
                <?php if ($editar_funcao): ?>
                <a href="<?= base_url('/admin/curriculos/funcoes.php') ?>" class="btn btn-ghost">Cancelar</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?= $editar_funcao ? 'Salvar Alteração' : 'Criar Função' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
