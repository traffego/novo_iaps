<?php
// admin/projetos/listar.php — Lista todos os projetos com ações
declare(strict_types=1);

require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

auth_require();

// ── Toggle ativo/inativo ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_ativo') {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $projeto = db_fetch('SELECT id, ativo FROM tab_projetos WHERE id = ?', [$id]);
        if ($projeto) {
            $novo_valor = $projeto['ativo'] ? 0 : 1;
            db_update('tab_projetos', ['ativo' => $novo_valor], 'id = ?', [$id]);
            $msg = $novo_valor ? 'Projeto ativado com sucesso.' : 'Projeto desativado com sucesso.';
            flash('success', $msg);
        }
    }
    redirect('/admin/projetos/listar.php');
}

// ── Listagem ──────────────────────────────────────────────────────────────────
$projetos = db_fetch_all(
    'SELECT p.*, ps.projetos_status AS nome_status
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     ORDER BY p.ativo DESC, p.nome_projeto ASC'
);

$page_title = 'Admin — Projetos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/admin/dashboard.php'],
    ['label' => 'Projetos', 'url' => '']
];

ob_start();
?>
<div class="page-header mb-6" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 class="page-title">Projetos</h2>
        <p class="text-muted"><?= count($projetos) ?> projeto(s) cadastrado(s)</p>
    </div>
    <a href="/admin/projetos/criar.php" class="btn btn-primary">+ Novo Projeto</a>
</div>

<?php if ($msg_flash = flash('success')): ?>
    <div class="alert alert-success mb-4"><?= e($msg_flash) ?></div>
<?php endif; ?>
<?php if ($msg_flash = flash('error')): ?>
    <div class="alert alert-danger mb-4"><?= e($msg_flash) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome do Projeto</th>
                    <th>Nº Proposta</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ativo</th>
                    <th style="width: 220px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projetos)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-8">Nenhum projeto cadastrado ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projetos as $proj): ?>
                        <tr>
                            <td>
                                <strong><?= e($proj['nome_projeto']) ?></strong>
                                <?php if (!empty($proj['termo_fomento'])): ?>
                                    <br><small class="text-muted"><?= e($proj['termo_fomento']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= e($proj['num_proposta'] ?? '—') ?></td>
                            <td>
                                <?= !empty($proj['valor']) ? e($proj['valor']) : '—' ?>
                            </td>
                            <td>
                                <?php if (!empty($proj['nome_status'])): ?>
                                    <span class="badge badge-info"><?= e($proj['nome_status']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($proj['ativo']): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                    <a href="/admin/projetos/editar.php?id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                    <a href="/admin/projetos/documentos.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Docs</a>
                                    <a href="/admin/projetos/funcoes.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-secondary">Funções</a>

                                    <!-- Form toggle ativo -->
                                    <form method="POST" action="/admin/projetos/listar.php" style="display: inline;" onsubmit="return confirm('Confirma a alteração?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="toggle_ativo">
                                        <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $proj['ativo'] ? 'btn-warning' : 'btn-success' ?>">
                                            <?= $proj['ativo'] ? 'Desativar' : 'Ativar' ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
