<?php
/**
 * Admin: Editar Projeto
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto     = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$status_list = db_fetch_all('SELECT * FROM tab_projetos_status ORDER BY id');
$erros       = [];

$page_title = 'Admin — Editar Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Editar: ' . truncate($projeto['nome_projeto'], 40)],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_projeto'    => trim($_POST['nome_projeto'] ?? ''),
        'num_proposta'    => trim($_POST['num_proposta'] ?? ''),
        'termo_fomento'   => trim($_POST['termo_fomento'] ?? ''),
        'valor'           => trim($_POST['valor'] ?? ''),
        'data_assinatura' => $_POST['data_assinatura'] ?: null,
        'publicacao_dou'  => $_POST['publicacao_dou'] ?: null,
        'inicio_vigencia' => $_POST['inicio_vigencia'] ?: null,
        'termino_vigencia'=> $_POST['termino_vigencia'] ?: null,
        'prestacao_contas'=> $_POST['prestacao_contas'] ?: null,
        'projeto_status'  => (int)($_POST['projeto_status'] ?? 0) ?: null,
        'objeto'          => trim($_POST['objeto'] ?? ''),
        'apresentacao'    => $_POST['apresentacao'] ?? '',
        'mostra_inicial'  => isset($_POST['mostra_inicial']) ? 1 : 0,
        'ultima_alteracao'=> auth_user()['id'],
        'data_ultima_alteracao' => date('Y-m-d'),
    ];

    if (empty($dados['nome_projeto'])) $erros[] = 'Nome do projeto é obrigatório.';

    if (empty($erros)) {
        db_update('tab_projetos', $dados, 'id = ?', [$id]);
        flash('success', 'Projeto atualizado com sucesso!');
        redirect(base_url('/admin/projetos/listar.php'));
    }
    // Repopular com os novos dados em caso de erro
    $projeto = array_merge($projeto, $dados);
}

ob_start();
?>
<?php if (!empty($erros)): ?>
<div class="alert alert-error"><?php foreach ($erros as $e) echo '<p>' . htmlspecialchars($e, ENT_QUOTES) . '</p>'; ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-actions-top">
        <a href="<?= base_url('/admin/projetos/documentos.php?projeto_id=' . $id) ?>" class="btn btn-outline btn-sm">📄 Documentos</a>
        <a href="<?= base_url('/admin/projetos/funcoes.php?projeto_id=' . $id) ?>" class="btn btn-outline btn-sm">👤 Funções</a>
    </div>
    <form method="POST" id="form-projeto">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group form-span-2">
                <label for="nome_projeto" class="form-label">Nome do Projeto <span class="required">*</span></label>
                <input type="text" id="nome_projeto" name="nome_projeto" class="form-input" required value="<?= e($projeto['nome_projeto']) ?>">
            </div>
            <div class="form-group">
                <label for="num_proposta" class="form-label">Número da Proposta</label>
                <input type="text" id="num_proposta" name="num_proposta" class="form-input" value="<?= e($projeto['num_proposta'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="termo_fomento" class="form-label">Termo de Fomento</label>
                <input type="text" id="termo_fomento" name="termo_fomento" class="form-input" value="<?= e($projeto['termo_fomento'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="valor" class="form-label">Valor</label>
                <input type="text" id="valor" name="valor" class="form-input" value="<?= e($projeto['valor'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="projeto_status" class="form-label">Status</label>
                <select id="projeto_status" name="projeto_status" class="form-select">
                    <option value="">Selecione</option>
                    <?php foreach ($status_list as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (int)$projeto['projeto_status'] === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['projetos_status']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data_assinatura" class="form-label">Data de Assinatura</label>
                <input type="date" id="data_assinatura" name="data_assinatura" class="form-input" value="<?= e($projeto['data_assinatura'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="publicacao_dou" class="form-label">Publicação DOU</label>
                <input type="date" id="publicacao_dou" name="publicacao_dou" class="form-input" value="<?= e($projeto['publicacao_dou'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="inicio_vigencia" class="form-label">Início da Vigência</label>
                <input type="date" id="inicio_vigencia" name="inicio_vigencia" class="form-input" value="<?= e($projeto['inicio_vigencia'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="termino_vigencia" class="form-label">Término da Vigência</label>
                <input type="date" id="termino_vigencia" name="termino_vigencia" class="form-input" value="<?= e($projeto['termino_vigencia'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="prestacao_contas" class="form-label">Prestação de Contas</label>
                <input type="date" id="prestacao_contas" name="prestacao_contas" class="form-input" value="<?= e($projeto['prestacao_contas'] ?? '') ?>">
            </div>
            <div class="form-group form-span-2">
                <label class="checkbox-label">
                    <input type="checkbox" name="mostra_inicial" value="1" <?= $projeto['mostra_inicial'] ? 'checked' : '' ?>>
                    <span>Exibir na página inicial</span>
                </label>
            </div>
            <div class="form-group form-span-2">
                <label for="objeto" class="form-label">Objeto do Projeto</label>
                <textarea id="objeto" name="objeto" class="form-textarea" rows="3"><?= e($projeto['objeto'] ?? '') ?></textarea>
            </div>
            <div class="form-group form-span-2">
                <label for="apresentacao" class="form-label">Apresentação Completa</label>
                <textarea id="apresentacao" name="apresentacao" class="form-textarea tinymce-editor" rows="10"><?= $projeto['apresentacao'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('/admin/projetos/listar.php') ?>" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
