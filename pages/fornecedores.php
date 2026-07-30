<?php
/**
 * Fornecedores — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';

$page_title       = 'Cadastro de Fornecedores';
$page_description = 'Cadastre sua empresa como fornecedor do Instituto Atleta Para Sempre e faça parte de nossa rede de parceiros.';

$estados = db_fetch_all('SELECT idUf, nome FROM estado ORDER BY nome');
$erros   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_fantasia'     => trim($_POST['nome_fantasia'] ?? ''),
        'razao_social'      => trim($_POST['razao_social'] ?? ''),
        'cnpj'              => trim($_POST['cnpj'] ?? ''),
        'endereco'          => trim($_POST['endereco'] ?? ''),
        'bairro'            => trim($_POST['bairro'] ?? ''),
        'cidade'            => trim($_POST['cidade'] ?? ''),
        'estado'            => trim($_POST['estado'] ?? ''),
        'cep'               => trim($_POST['cep'] ?? ''),
        'contato_nome'      => trim($_POST['contato_nome'] ?? ''),
        'contato_cargo'     => trim($_POST['contato_cargo'] ?? ''),
        'contato_telefone'  => trim($_POST['contato_telefone'] ?? ''),
        'contato_email'     => trim($_POST['contato_email'] ?? ''),
    ];

    if (empty($dados['razao_social']))   $erros[] = 'Razão social é obrigatória.';
    if (empty($dados['cnpj']))           $erros[] = 'CNPJ é obrigatório.';
    if (empty($dados['contato_nome']))   $erros[] = 'Nome do contato é obrigatório.';
    if (empty($dados['contato_telefone'])) $erros[] = 'Telefone do contato é obrigatório.';
    if (!empty($dados['contato_email']) && !filter_var($dados['contato_email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail do contato inválido.';
    }

    if (empty($erros)) {
        db_insert('tab_fornecedores', $dados);
        flash('success', 'Cadastro realizado com sucesso! Entraremos em contato em breve.');
        redirect('/fornecedores');
    } else {
        foreach ($dados as $k => $v) $_SESSION['old'][$k] = $v;
    }
}

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Fornecedores</span>
        </nav>
        <h1 class="page-hero-title">Cadastro de Fornecedores</h1>
        <p class="page-hero-sub">Faça parte da nossa rede de fornecedores e parceiros.</p>
    </div>
</section>

<section class="section" id="fornecedores">
    <div class="container container-narrow">

        <div id="js-form-errors" class="alert alert-error mb-6" style="display:none;" role="alert">
            <strong>Por favor, corrija os campos apontados abaixo:</strong>
            <ul id="js-errors-list"></ul>
        </div>

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error mb-6" role="alert">
            <strong>Por favor, corrija os erros abaixo:</strong>
            <ul><?php foreach ($erros as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <div class="form-card fade-in-up">
            <form method="POST" action="/fornecedores" id="form-fornecedor">
                <?= csrf_field() ?>

                <!-- DADOS DA EMPRESA -->
                <div class="form-section">
                    <h3 class="form-section-title"><span class="form-section-num">1</span> Dados da Empresa</h3>
                    <div class="form-row">
                        <div class="form-group form-group-wide">
                            <label for="razao_social" class="form-label">Razão Social <span class="required">*</span></label>
                            <input type="text" id="razao_social" name="razao_social" class="form-input" required
                                   value="<?= e(old('razao_social')) ?>" placeholder="Razão social completa">
                        </div>
                        <div class="form-group">
                            <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                            <input type="text" id="nome_fantasia" name="nome_fantasia" class="form-input"
                                   value="<?= e(old('nome_fantasia')) ?>" placeholder="Nome fantasia">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cnpj" class="form-label">CNPJ <span class="required">*</span></label>
                        <input type="text" id="cnpj" name="cnpj" class="form-input" required
                               value="<?= e(old('cnpj')) ?>" placeholder="00.000.000/0000-00"
                               data-mask="cnpj" maxlength="18">
                    </div>
                </div>

                <!-- ENDEREÇO -->
                <div class="form-section">
                    <h3 class="form-section-title"><span class="form-section-num">2</span> Endereço</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep_forn" class="form-label">CEP</label>
                            <input type="text" id="cep_forn" name="cep" class="form-input"
                                   value="<?= e(old('cep')) ?>" placeholder="00000-000" data-mask="cep" maxlength="9">
                        </div>
                        <div class="form-group form-group-wide">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" id="endereco" name="endereco" class="form-input"
                                   value="<?= e(old('endereco')) ?>" placeholder="Rua, número, complemento">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-input"
                                   value="<?= e(old('bairro')) ?>" placeholder="Bairro">
                        </div>
                        <div class="form-group">
                            <label for="estado_forn" class="form-label">Estado</label>
                            <select id="estado_forn" name="estado" class="form-select" data-estado-select="cidade_forn">
                                <option value="">Selecione</option>
                                <?php foreach ($estados as $uf): ?>
                                <option value="<?= e($uf['idUf']) ?>" <?= old('estado') === $uf['idUf'] ? 'selected' : '' ?>>
                                    <?= e($uf['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cidade_forn" class="form-label">Cidade</label>
                            <select id="cidade_forn" name="cidade" class="form-select"
                                    data-cidade-select="cidade_forn"
                                    data-valor-antigo="<?= e(old('cidade')) ?>">
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CONTATO -->
                <div class="form-section">
                    <h3 class="form-section-title"><span class="form-section-num">3</span> Pessoa de Contato</h3>
                    <div class="form-row">
                        <div class="form-group form-group-wide">
                            <label for="contato_nome" class="form-label">Nome do contato <span class="required">*</span></label>
                            <input type="text" id="contato_nome" name="contato_nome" class="form-input" required
                                   value="<?= e(old('contato_nome')) ?>" placeholder="Nome completo">
                        </div>
                        <div class="form-group">
                            <label for="contato_cargo" class="form-label">Cargo</label>
                            <input type="text" id="contato_cargo" name="contato_cargo" class="form-input"
                                   value="<?= e(old('contato_cargo')) ?>" placeholder="Ex: Gerente Comercial">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contato_telefone" class="form-label">Telefone <span class="required">*</span></label>
                            <input type="tel" id="contato_telefone" name="contato_telefone" class="form-input" required
                                   value="<?= e(old('contato_telefone')) ?>" placeholder="(11) 99999-9999" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="contato_email" class="form-label">E-mail</label>
                            <input type="email" id="contato_email" name="contato_email" class="form-input"
                                   value="<?= e(old('contato_email')) ?>" placeholder="contato@empresa.com.br">
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg btn-full" id="btn-cadastrar">
                        Realizar Cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
