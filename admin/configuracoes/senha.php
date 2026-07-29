<?php
/**
 * Admin: Alterar Senha
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Alterar Senha';
$breadcrumb = [
    ['label' => 'Dashboard',    'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Configurações'],
    ['label' => 'Senha'],
];

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $senha_atual     = $_POST['senha_atual'] ?? '';
    $nova_senha      = $_POST['nova_senha'] ?? '';
    $confirmar_nova  = $_POST['confirmar_nova_senha'] ?? '';

    // Buscar hash atual
    $usuario = db_fetch('SELECT id, senha FROM tab_login WHERE id = ?', [auth_user()['id']]);

    if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
        $erros[] = 'Senha atual incorreta.';
    }
    if (strlen($nova_senha) < 8) {
        $erros[] = 'A nova senha deve ter pelo menos 8 caracteres.';
    }
    if ($nova_senha !== $confirmar_nova) {
        $erros[] = 'A confirmação da nova senha não confere.';
    }

    if (empty($erros)) {
        db_update(
            'tab_login',
            ['senha' => password_hash($nova_senha, PASSWORD_BCRYPT)],
            'id = ?',
            [$usuario['id']]
        );
        flash('success', 'Senha alterada com sucesso!');
        redirect(base_url('/admin/configuracoes/senha.php'));
    }
}

ob_start();
?>
<?php if (!empty($erros)): ?>
<div class="alert alert-error">
    <?php foreach ($erros as $e) echo '<p>' . htmlspecialchars($e, ENT_QUOTES) . '</p>'; ?>
</div>
<?php endif; ?>

<div class="admin-card" style="max-width:480px">
    <form method="POST" id="form-senha">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="senha_atual" class="form-label">Senha atual <span class="required">*</span></label>
            <input type="password" id="senha_atual" name="senha_atual" class="form-input" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="nova_senha" class="form-label">Nova senha <span class="required">*</span></label>
            <input type="password" id="nova_senha" name="nova_senha" class="form-input" required autocomplete="new-password" minlength="8">
            <small class="form-hint">Mínimo de 8 caracteres.</small>
        </div>
        <div class="form-group">
            <label for="confirmar_nova_senha" class="form-label">Confirmar nova senha <span class="required">*</span></label>
            <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" class="form-input" required autocomplete="new-password" minlength="8">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Alterar Senha</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
