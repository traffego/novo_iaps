<?php
/**
 * Admin: Dados da Organização
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Dados da Organização';
$breadcrumb = [
    ['label' => 'Dashboard',    'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Configurações'],
    ['label' => 'Organização'],
];

try {
    db_execute("ALTER TABLE tab_org ADD COLUMN tema_padrao VARCHAR(10) DEFAULT 'dark'");
} catch (Throwable $e) {}

$org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_org'     => trim($_POST['nome_org'] ?? ''),
        'presidente_org' => trim($_POST['presidente_org'] ?? ''),
        'telefone'     => trim($_POST['telefone'] ?? ''),
        'e_mail'       => trim($_POST['e_mail'] ?? ''),
        'endereco'     => trim($_POST['endereco'] ?? ''),
        'bairro'       => trim($_POST['bairro'] ?? ''),
        'cidade'       => trim($_POST['cidade'] ?? ''),
        'estado'       => trim($_POST['estado'] ?? ''),
        'cep'          => trim($_POST['cep'] ?? ''),
        'site'         => trim($_POST['site'] ?? ''),
        'tema_padrao'  => trim($_POST['tema_padrao'] ?? 'dark'),
        'liberado'     => isset($_POST['liberado']) ? 1 : 0,
    ];

    if ($org) {
        db_update('tab_org', $dados, 'cod_org = 10001', []);
    } else {
        $dados['cod_org'] = 10001;
        db_insert('tab_org', $dados);
    }

    flash('success', 'Dados da organização e tema padrão atualizados!');
    redirect(base_url('/admin/configuracoes/organizacao.php'));
}

// Preencher com dados atuais
$org = $org ?: [];

ob_start();
?>
<div class="admin-card" style="max-width:700px">
    <form method="POST" id="form-org">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group form-span-2">
                <label for="nome_org" class="form-label">Nome da Organização</label>
                <input type="text" id="nome_org" name="nome_org" class="form-input" value="<?= e($org['nome_org'] ?? '') ?>">
            </div>
            <div class="form-group form-span-2">
                <label for="presidente_org" class="form-label">Presidente / Responsável</label>
                <input type="text" id="presidente_org" name="presidente_org" class="form-input" value="<?= e($org['presidente_org'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-input" value="<?= e($org['telefone'] ?? '') ?>" data-mask="telefone">
            </div>
            <div class="form-group">
                <label for="e_mail" class="form-label">E-mail</label>
                <input type="email" id="e_mail" name="e_mail" class="form-input" value="<?= e($org['e_mail'] ?? '') ?>">
            </div>
            <div class="form-group form-span-2">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="form-input" value="<?= e($org['endereco'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" id="bairro" name="bairro" class="form-input" value="<?= e($org['bairro'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" id="cidade" name="cidade" class="form-input" value="<?= e($org['cidade'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="estado" class="form-label">Estado (UF)</label>
                <input type="text" id="estado" name="estado" class="form-input" value="<?= e($org['estado'] ?? '') ?>" maxlength="2" style="max-width:80px">
            </div>
            <div class="form-group">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" id="cep" name="cep" class="form-input" value="<?= e($org['cep'] ?? '') ?>" data-mask="cep">
            </div>
            <div class="form-group form-span-2">
                <label for="site" class="form-label">Site</label>
                <input type="url" id="site" name="site" class="form-input" value="<?= e($org['site'] ?? '') ?>" placeholder="https://...">
            </div>
            <div class="form-group form-span-2">
                <label for="tema_padrao" class="form-label">Tema Padrão do Site Público (para novos visitantes)</label>
                <select id="tema_padrao" name="tema_padrao" class="form-select">
                    <option value="dark" <?= ($org['tema_padrao'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>🌙 Modo Escuro (Dark Mode)</option>
                    <option value="light" <?= ($org['tema_padrao'] ?? 'dark') === 'light' ? 'selected' : '' ?>>☀️ Modo Claro (Light Mode)</option>
                </select>
                <small class="form-text text-muted" style="display:block; margin-top:0.35rem; color:var(--adm-text-muted);">Define o tema visual padrão que será exibido aos visitantes do site que ainda não escolheram uma preferência.</small>
            </div>
            <div class="form-group form-span-2">
                <label class="checkbox-label">
                    <input type="checkbox" name="liberado" value="1" <?= !empty($org['liberado']) ? 'checked' : '' ?>>
                    <span>Organização liberada para acesso ao sistema</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
