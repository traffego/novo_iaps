<?php
/**
 * Admin: Funções do Projeto
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$projeto_id = (int)($_GET['projeto_id'] ?? 0);
if (!$projeto_id) { flash('error', 'Projeto não especificado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$projeto_id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    if ($action === 'link') {
        $funcao_id = (int)($_POST['funcao_id'] ?? 0);
        if ($funcao_id) {
            // Evitar duplicata
            $existe = db_count('SELECT COUNT(*) FROM tab_projetos_funcao WHERE id_projeto = ? AND id_funcao = ?', [$projeto_id, $funcao_id]);
            if (!$existe) {
                db_insert('tab_projetos_funcao', [
                    'id_projeto'    => $projeto_id,
                    'id_funcao'     => $funcao_id,
                    'cadastrado_por'=> auth_user()['id'],
                    'data_cadastro' => date('Y-m-d'),
                ]);
                flash('success', 'Função vinculada com sucesso!');
            } else {
                flash('error', 'Esta função já está vinculada ao projeto.');
            }
        }
    } elseif ($action === 'unlink') {
        $funcao_id = (int)($_POST['funcao_id'] ?? 0);
        if ($funcao_id) {
            db_delete('tab_projetos_funcao', 'id_projeto = ? AND id_funcao = ?', [$projeto_id, $funcao_id]);
            flash('success', 'Função desvinculada.');
        }
    }
    redirect(base_url('/admin/projetos/funcoes.php?projeto_id=' . $projeto_id));
}

$funcoes_vinculadas = db_fetch_all(
    'SELECT DISTINCT f.id, f.funcao, f.ativo FROM tab_curriculos_funcao f
     INNER JOIN tab_projetos_funcao pf ON f.id = pf.id_funcao
     WHERE pf.id_projeto = ? ORDER BY f.funcao',
    [$projeto_id]
);

$ids_vinculados = array_column($funcoes_vinculadas, 'id');

$funcoes_disponiveis = db_fetch_all(
    'SELECT * FROM tab_curriculos_funcao WHERE ativo = 1 ORDER BY funcao'
);
$funcoes_disponiveis = array_filter($funcoes_disponiveis, fn($f) => !in_array($f['id'], $ids_vinculados));

$page_title = 'Admin — Funções do Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Funções: ' . truncate($projeto['nome_projeto'], 35)],
];

ob_start();
?>
<div class="page-section-header">
    <h2><?= e($projeto['nome_projeto']) ?></h2>
    <a href="<?= base_url('/admin/projetos/editar.php?id=' . $projeto_id) ?>" class="btn btn-outline btn-sm">← Voltar ao projeto</a>
</div>

<div class="admin-forms-grid">
    <!-- Funções vinculadas -->
    <div class="admin-card">
        <h3 class="admin-card-title">Funções Vinculadas (<?= count($funcoes_vinculadas) ?>)</h3>
        <?php if (empty($funcoes_vinculadas)): ?>
        <p class="text-muted">Nenhuma função vinculada ainda.</p>
        <?php else: ?>
        <ul class="funcoes-list">
            <?php foreach ($funcoes_vinculadas as $f): ?>
            <li class="funcao-item">
                <span><?= e($f['funcao']) ?></span>
                <form method="POST" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="unlink">
                    <input type="hidden" name="funcao_id" value="<?= $f['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Desvincular esta função do projeto?">Remover</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <!-- Vincular nova função -->
    <div class="admin-card">
        <h3 class="admin-card-title">Vincular Função</h3>
        <?php if (empty($funcoes_disponiveis)): ?>
        <p class="text-muted">Todas as funções ativas já estão vinculadas.</p>
        <?php else: ?>
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="link">
            <div class="form-group">
                <label for="funcao_id" class="form-label">Selecione a Função</label>
                <select id="funcao_id" name="funcao_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($funcoes_disponiveis as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= e($f['funcao']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Vincular Função</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
