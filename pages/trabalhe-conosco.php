<?php
/**
 * Trabalhe Conosco — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/upload.php';

$page_title       = 'Trabalhe Conosco';
$page_description = 'Envie seu currículo para o Instituto Atleta Para Sempre e faça parte de um time comprometido com o esporte e o desenvolvimento social.';

// Dados para os selects
$projetos = db_fetch_all('SELECT id, nome_projeto FROM tab_projetos WHERE ativo = 1 ORDER BY nome_projeto');
$estados  = db_fetch_all('SELECT idUf, nome FROM estado ORDER BY nome');

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // Coletar todos os dados
    $dados = [
        'nome'                    => trim($_POST['nome'] ?? ''),
        'sexo'                    => trim($_POST['sexo'] ?? ''),
        'data_nascimento'         => trim($_POST['data_nascimento'] ?? ''),
        'estado_civil'            => trim($_POST['estado_civil'] ?? ''),
        'telefone_1'              => trim($_POST['telefone_1'] ?? ''),
        'telefone_2'              => trim($_POST['telefone_2'] ?? ''),
        'e_mail'                  => trim($_POST['e_mail'] ?? ''),
        'cep'                     => trim($_POST['cep'] ?? ''),
        'endereco'                => trim($_POST['endereco'] ?? ''),
        'bairro'                  => trim($_POST['bairro'] ?? ''),
        'estado'                  => trim($_POST['estado'] ?? ''),
        'cidade'                  => trim($_POST['cidade'] ?? ''),
        'id_projeto'              => (int)($_POST['id_projeto'] ?? 0),
        'id_funcao'               => (int)($_POST['id_funcao'] ?? 0),
        'escolaridade'            => trim($_POST['escolaridade'] ?? ''),
        'idioma_sim_nao'          => (int)($_POST['idioma_sim_nao'] ?? 0),
        'idioma_quais'            => trim($_POST['idioma_quais'] ?? ''),
        'informatica_sim_nao'     => (int)($_POST['informatica_sim_nao'] ?? 0),
        'cursos_relevantes'       => trim($_POST['cursos_relevantes'] ?? ''),
        'experiencia_sim_nao'     => (int)($_POST['experiencia_sim_nao'] ?? 0),
        'empresa_1'               => trim($_POST['empresa_1'] ?? ''),
        'empresa_1_periodo'       => trim($_POST['empresa_1_periodo'] ?? ''),
        'empresa_1_funcao'        => trim($_POST['empresa_1_funcao'] ?? ''),
        'empresa_2'               => trim($_POST['empresa_2'] ?? ''),
        'empresa_2_periodo'       => trim($_POST['empresa_2_periodo'] ?? ''),
        'empresa_2_funcao'        => trim($_POST['empresa_2_funcao'] ?? ''),
        'empresa_3'               => trim($_POST['empresa_3'] ?? ''),
        'empresa_3_periodo'       => trim($_POST['empresa_3_periodo'] ?? ''),
        'empresa_3_funcao'        => trim($_POST['empresa_3_funcao'] ?? ''),
        'experiencia_profissional' => trim($_POST['experiencia_profissional'] ?? ''),
    ];

    // Validações
    if (empty($dados['nome']))          $erros[] = 'Nome é obrigatório.';
    if (empty($dados['sexo']))          $erros[] = 'Sexo é obrigatório.';
    if (empty($dados['data_nascimento'])) $erros[] = 'Data de nascimento é obrigatória.';
    if (empty($dados['telefone_1']))    $erros[] = 'Telefone principal é obrigatório.';
    if (empty($dados['endereco']))      $erros[] = 'Endereço é obrigatório.';
    if (empty($dados['bairro']))        $erros[] = 'Bairro é obrigatório.';
    if (empty($dados['cep']))           $erros[] = 'CEP é obrigatório.';
    if (empty($dados['estado']))        $erros[] = 'Estado é obrigatório.';
    if (empty($dados['cidade']))        $erros[] = 'Cidade é obrigatória.';
    if (!$dados['id_projeto'])          $erros[] = 'Selecione um projeto.';
    if (!$dados['id_funcao'])           $erros[] = 'Selecione uma função/cargo.';
    if (empty($dados['escolaridade']))  $erros[] = 'Escolaridade é obrigatória.';
    if (!isset($_FILES['arquivo_curriculo']) || $_FILES['arquivo_curriculo']['error'] !== UPLOAD_ERR_OK) {
        $erros[] = 'Envio do currículo em PDF é obrigatório.';
    }

    if (empty($erros)) {
        // Upload do PDF
        $upload = upload_file($_FILES['arquivo_curriculo'], UPLOAD_PATH . '/curriculos', ['pdf']);
        if (!$upload['success']) {
            $erros[] = 'Erro no upload do currículo: ' . $upload['error'];
        } else {
            $dados['arquivo_curriculo'] = $upload['filename'];
            db_insert('tab_curriculos', $dados);
            flash('success', 'Currículo enviado com sucesso! Entraremos em contato em breve.');
            redirect('/trabalhe-conosco');
        }
    }

    // Repopular dados antigos em caso de erro
    foreach ($dados as $k => $v) {
        $_SESSION['old'][$k] = $v;
    }
}

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Trabalhe Conosco</span>
        </nav>
        <h1 class="page-hero-title">Trabalhe Conosco</h1>
        <p class="page-hero-sub">Faça parte do time que transforma vidas através do esporte.</p>
    </div>
</section>

<section class="section" id="candidatura">
    <div class="container container-narrow">

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error" role="alert">
            <strong>Por favor, corrija os erros abaixo:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="form-card fade-in-up">
            <form method="POST" action="/trabalhe-conosco" enctype="multipart/form-data" id="form-curriculo" novalidate>
                <?= csrf_field() ?>

                <!-- DADOS PESSOAIS -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">1</span> Dados Pessoais
                    </h3>
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-input" required
                               value="<?= e(old('nome')) ?>" placeholder="Seu nome completo" autocomplete="name">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sexo <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="sexo" value="Masculino" <?= old('sexo') === 'Masculino' ? 'checked' : '' ?> required>
                                    <span>Masculino</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="sexo" value="Feminino" <?= old('sexo') === 'Feminino' ? 'checked' : '' ?>>
                                    <span>Feminino</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="data_nascimento" class="form-label">Data de Nascimento <span class="required">*</span></label>
                            <input type="date" id="data_nascimento" name="data_nascimento" class="form-input" required
                                   value="<?= e(old('data_nascimento')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="estado_civil" class="form-label">Estado Civil <span class="required">*</span></label>
                            <select id="estado_civil" name="estado_civil" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach (['Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viúvo(a)', 'União Estável'] as $ec): ?>
                                <option value="<?= e($ec) ?>" <?= old('estado_civil') === $ec ? 'selected' : '' ?>><?= e($ec) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone_1" class="form-label">Telefone principal <span class="required">*</span></label>
                            <input type="tel" id="telefone_1" name="telefone_1" class="form-input" required
                                   value="<?= e(old('telefone_1')) ?>" placeholder="(11) 99999-9999" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="telefone_2" class="form-label">Telefone secundário</label>
                            <input type="tel" id="telefone_2" name="telefone_2" class="form-input"
                                   value="<?= e(old('telefone_2')) ?>" placeholder="(11) 99999-9999" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="e_mail" class="form-label">E-mail</label>
                            <input type="email" id="e_mail" name="e_mail" class="form-input"
                                   value="<?= e(old('e_mail')) ?>" placeholder="seu@email.com" autocomplete="email">
                        </div>
                    </div>
                </div>

                <!-- ENDEREÇO -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">2</span> Endereço
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep" class="form-label">CEP <span class="required">*</span></label>
                            <input type="text" id="cep" name="cep" class="form-input" required
                                   value="<?= e(old('cep')) ?>" placeholder="00000-000" data-mask="cep" maxlength="9">
                        </div>
                        <div class="form-group form-group-wide">
                            <label for="endereco" class="form-label">Endereço <span class="required">*</span></label>
                            <input type="text" id="endereco" name="endereco" class="form-input" required
                                   value="<?= e(old('endereco')) ?>" placeholder="Rua, nº, complemento" autocomplete="street-address">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro <span class="required">*</span></label>
                            <input type="text" id="bairro" name="bairro" class="form-input" required
                                   value="<?= e(old('bairro')) ?>" placeholder="Bairro">
                        </div>
                        <div class="form-group">
                            <label for="estado_res" class="form-label">Estado <span class="required">*</span></label>
                            <select id="estado_res" name="estado" class="form-select" required
                                    data-estado-select="cidade_res">
                                <option value="">Selecione</option>
                                <?php foreach ($estados as $uf): ?>
                                <option value="<?= e($uf['idUf']) ?>"
                                    <?= old('estado') === $uf['idUf'] ? 'selected' : '' ?>
                                    data-valor-antigo-target="cidade_res">
                                    <?= e($uf['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cidade_res" class="form-label">Cidade <span class="required">*</span></label>
                            <select id="cidade_res" name="cidade" class="form-select" required
                                    data-cidade-select="cidade_res"
                                    data-valor-antigo="<?= e(old('cidade')) ?>">
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- VAGA -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">3</span> Vaga de Interesse
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="projeto" class="form-label">Projeto <span class="required">*</span></label>
                            <select id="projeto" name="id_projeto" class="form-select" required data-projeto-select>
                                <option value="">Selecione o projeto</option>
                                <?php foreach ($projetos as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (int)old('id_projeto') === (int)$p['id'] ? 'selected' : '' ?>>
                                    <?= e($p['nome_projeto']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="funcao" class="form-label">Função/Cargo <span class="required">*</span></label>
                            <select id="funcao" name="id_funcao" class="form-select" required data-funcao-select>
                                <option value="">Selecione o projeto primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- FORMAÇÃO -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">4</span> Formação Acadêmica
                    </h3>
                    <div class="form-group">
                        <label for="escolaridade" class="form-label">Escolaridade <span class="required">*</span></label>
                        <select id="escolaridade" name="escolaridade" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach (['Ensino Fundamental Incompleto','Ensino Fundamental Completo','Ensino Médio Incompleto','Ensino Médio Completo','Ensino Superior Incompleto','Ensino Superior Completo','Pós-Graduação','Mestrado','Doutorado'] as $esc): ?>
                            <option value="<?= e($esc) ?>" <?= old('escolaridade') === $esc ? 'selected' : '' ?>><?= e($esc) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Possui idiomas? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="idioma_sim_nao" value="1" <?= old('idioma_sim_nao') == '1' ? 'checked' : '' ?>>
                                <span>Sim</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="idioma_sim_nao" value="0" <?= old('idioma_sim_nao') == '0' || old('idioma_sim_nao') === '' ? 'checked' : '' ?>>
                                <span>Não</span>
                            </label>
                        </div>
                    </div>
                    <div id="bloco-idiomas" style="display:none">
                        <div class="form-group">
                            <label for="idioma_quais" class="form-label">Quais idiomas?</label>
                            <input type="text" id="idioma_quais" name="idioma_quais" class="form-input"
                                   value="<?= e(old('idioma_quais')) ?>" placeholder="Ex: Inglês (avançado), Espanhol (básico)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Possui conhecimentos em informática?</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="informatica_sim_nao" value="1" <?= old('informatica_sim_nao') == '1' ? 'checked' : '' ?>>
                                <span>Sim</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="informatica_sim_nao" value="0" <?= old('informatica_sim_nao') == '0' || old('informatica_sim_nao') === '' ? 'checked' : '' ?>>
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cursos_relevantes" class="form-label">Cursos e qualificações relevantes</label>
                        <textarea id="cursos_relevantes" name="cursos_relevantes" class="form-textarea" rows="3"
                                  placeholder="Liste cursos, certificações e treinamentos relevantes..."><?= e(old('cursos_relevantes')) ?></textarea>
                    </div>
                </div>

                <!-- EXPERIÊNCIA -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">5</span> Experiência Profissional
                    </h3>
                    <div class="form-group">
                        <label class="form-label">Possui experiência profissional? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="experiencia_sim_nao" value="1" <?= old('experiencia_sim_nao') == '1' ? 'checked' : '' ?>>
                                <span>Sim</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="experiencia_sim_nao" value="0" <?= old('experiencia_sim_nao') == '0' || old('experiencia_sim_nao') === '' ? 'checked' : '' ?>>
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <div id="bloco-experiencias" style="display:none">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="experience-block">
                            <h4>Empresa <?= $i ?></h4>
                            <div class="form-row">
                                <div class="form-group form-group-wide">
                                    <label for="empresa_<?= $i ?>" class="form-label">Nome da empresa</label>
                                    <input type="text" id="empresa_<?= $i ?>" name="empresa_<?= $i ?>" class="form-input"
                                           value="<?= e(old("empresa_$i")) ?>" placeholder="Nome da empresa">
                                </div>
                                <div class="form-group">
                                    <label for="empresa_<?= $i ?>_periodo" class="form-label">Período</label>
                                    <input type="text" id="empresa_<?= $i ?>_periodo" name="empresa_<?= $i ?>_periodo" class="form-input"
                                           value="<?= e(old("empresa_{$i}_periodo")) ?>" placeholder="Ex: 01/2020 - 12/2022">
                                </div>
                                <div class="form-group">
                                    <label for="empresa_<?= $i ?>_funcao" class="form-label">Função exercida</label>
                                    <input type="text" id="empresa_<?= $i ?>_funcao" name="empresa_<?= $i ?>_funcao" class="form-input"
                                           value="<?= e(old("empresa_{$i}_funcao")) ?>" placeholder="Cargo/função">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <div class="form-group">
                            <label for="experiencia_profissional" class="form-label">Resumo da experiência profissional</label>
                            <textarea id="experiencia_profissional" name="experiencia_profissional" class="form-textarea" rows="4"
                                      placeholder="Descreva brevemente sua trajetória profissional..."><?= e(old('experiencia_profissional')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- UPLOAD DO CURRÍCULO -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <span class="form-section-num">6</span> Currículo
                    </h3>
                    <div class="form-group">
                        <label for="arquivo_curriculo" class="form-label">Arquivo PDF do currículo <span class="required">*</span></label>
                        <div class="file-upload-area">
                            <input type="file" id="arquivo_curriculo" name="arquivo_curriculo"
                                   accept=".pdf" required
                                   data-accept="pdf" data-max-mb="16"
                                   data-nome-alvo="#nome-arquivo-curriculo">
                            <label for="arquivo_curriculo" class="file-upload-label">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span>Clique para selecionar ou arraste o PDF aqui</span>
                                <small>Formato: PDF | Tamanho máximo: 16MB</small>
                            </label>
                            <span id="nome-arquivo-curriculo" class="file-name">Nenhum arquivo selecionado</span>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg btn-full" id="btn-enviar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Enviar Candidatura
                    </button>
                    <p class="form-disclaimer">Seus dados serão tratados com total confidencialidade conforme a LGPD.</p>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
