<?php
/**
 * Contato — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/mail.php';

$page_title       = 'Contato';
$page_description = 'Entre em contato com o Instituto Atleta Para Sempre. Tire suas dúvidas, proponha parcerias ou fale com nossa equipe.';

// Dados da organização
$org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001 LIMIT 1');

// Processar formulário
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $assunto  = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (empty($nome))     $erros[] = 'Nome é obrigatório.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if (empty($assunto))  $erros[] = 'Assunto é obrigatório.';
    if (empty($mensagem)) $erros[] = 'Mensagem é obrigatória.';

    if (empty($erros)) {
        $corpo = "
            <h2>Nova mensagem via site</h2>
            <p><strong>Nome:</strong> {$nome}</p>
            <p><strong>E-mail:</strong> {$email}</p>
            <p><strong>Telefone:</strong> {$telefone}</p>
            <p><strong>Assunto:</strong> {$assunto}</p>
            <p><strong>Mensagem:</strong><br>" . nl2br(htmlspecialchars($mensagem)) . "</p>
        ";
        $enviado = send_contact_mail($nome, $email, $telefone, $mensagem);
        if ($enviado) {
            flash('success', 'Mensagem enviada com sucesso! Retornaremos em breve.');
        } else {
            flash('error', 'Erro ao enviar mensagem. Tente novamente ou contate-nos por telefone.');
        }
        redirect('/contato');
    }
}

ob_start();
?>
<!-- HERO DA PÁGINA -->
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Contato</span>
        </nav>
        <h1 class="page-hero-title">Entre em Contato</h1>
        <p class="page-hero-sub">Estamos aqui para responder suas dúvidas e propostas.</p>
    </div>
</section>

<section class="section" id="contato">
    <div class="container">

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error" role="alert">
            <strong>Corrija os erros abaixo:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="contact-grid">
            <!-- FORMULÁRIO -->
            <div class="contact-form-wrap fade-in-up">
                <h2 class="form-title">Envie sua mensagem</h2>
                <form method="POST" action="/contato" id="form-contato" novalidate>
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="nome" class="form-label">Nome completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-input" required
                               value="<?= e(old('nome')) ?>" placeholder="Seu nome completo" autocomplete="name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">E-mail <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input" required
                                   value="<?= e(old('email')) ?>" placeholder="seu@email.com" autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-input"
                                   value="<?= e(old('telefone')) ?>" placeholder="(11) 99999-9999"
                                   data-mask="telefone" autocomplete="tel">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assunto" class="form-label">Assunto <span class="required">*</span></label>
                        <select id="assunto" name="assunto" class="form-select" required>
                            <option value="">Selecione o assunto</option>
                            <option value="Dúvida" <?= old('assunto') === 'Dúvida' ? 'selected' : '' ?>>Dúvida</option>
                            <option value="Parcerias" <?= old('assunto') === 'Parcerias' ? 'selected' : '' ?>>Parcerias</option>
                            <option value="Imprensa" <?= old('assunto') === 'Imprensa' ? 'selected' : '' ?>>Imprensa</option>
                            <option value="Outro" <?= old('assunto') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem" class="form-label">Mensagem <span class="required">*</span></label>
                        <textarea id="mensagem" name="mensagem" class="form-textarea" required rows="6"
                                  placeholder="Descreva sua mensagem..."><?= e(old('mensagem')) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-full" id="btn-enviar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Enviar Mensagem
                    </button>
                </form>
            </div>

            <!-- INFO DE CONTATO -->
            <div class="contact-info fade-in-up" style="animation-delay:.15s">
                <h2 class="form-title">Informações de Contato</h2>

                <?php if ($org): ?>
                <div class="contact-info-list">
                    <?php if ($org['telefone']): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.77a16 16 0 0 0 6.29 6.29l1.88-1.88a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <strong>Telefone</strong>
                            <p><?= e($org['telefone']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($org['e_mail']): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <strong>E-mail</strong>
                            <p><a href="mailto:<?= e($org['e_mail']) ?>"><?= e($org['e_mail']) ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($org['endereco']): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <strong>Endereço</strong>
                            <p><?= e($org['endereco']) ?><?= $org['bairro'] ? ', ' . e($org['bairro']) : '' ?></p>
                            <p><?= e($org['cidade']) ?> — <?= e($org['estado']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="contact-social">
                    <h4>Redes Sociais</h4>
                    <div class="social-links">
                        <a href="#" class="social-btn" aria-label="Facebook" target="_blank" rel="noopener">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="Instagram" target="_blank" rel="noopener">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
