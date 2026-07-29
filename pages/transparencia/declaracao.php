<?php
/**
 * Transparência: Declaração — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Declaração de Transparência';
$page_description = 'Declaração de transparência e compromisso público do Instituto Atleta Para Sempre.';

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span aria-hidden="true">›</span>
            <span>Transparência</span><span aria-hidden="true">›</span>
            <span>Declaração</span>
        </nav>
        <h1 class="page-hero-title">Declaração de Transparência</h1>
    </div>
</section>

<section class="section" id="declaracao">
    <div class="container container-article">
        <div class="transparency-card fade-in-up">
            <div class="transparency-icon">📋</div>
            <h2>Compromisso com a Transparência</h2>
            <p>O <strong>Instituto Atleta Para Sempre</strong>, no cumprimento de sua missão institucional e em atendimento à legislação vigente, declara publicamente seu compromisso com a transparência e a boa governança na gestão dos recursos públicos e privados que lhe são confiados.</p>
            <p>Em conformidade com a <strong>Lei de Incentivo ao Esporte (Lei nº 11.438/2006)</strong> e com a <strong>Lei de Acesso à Informação (Lei nº 12.527/2011)</strong>, disponibilizamos de forma pública e acessível:</p>
            <ul class="transparency-list">
                <li>✓ Estatuto Social e alterações</li>
                <li>✓ Identificação dos dirigentes e responsáveis</li>
                <li>✓ Relatórios financeiros e prestações de contas</li>
                <li>✓ Editais de seleção e homologações</li>
                <li>✓ Regulamento de compras e contratações</li>
                <li>✓ Termos de fomento e colaboração</li>
                <li>✓ Painéis de transferências legais e discricionárias</li>
            </ul>
            <p>Reafirmamos nosso compromisso com a ética, a legalidade e o uso responsável dos recursos destinados ao fomento do esporte brasileiro.</p>
        </div>

        <div class="transparency-nav-grid fade-in-up">
            <a href="/transparencia/dirigentes" class="transp-nav-card">
                <span class="transp-nav-icon">👤</span>
                <span>Dirigentes</span>
            </a>
            <a href="/transparencia/estatuto" class="transp-nav-card">
                <span class="transp-nav-icon">📜</span>
                <span>Estatuto</span>
            </a>
            <a href="/transparencia/financeiro" class="transp-nav-card">
                <span class="transp-nav-icon">💰</span>
                <span>Financeiro</span>
            </a>
            <a href="/transparencia/regulamento" class="transp-nav-card">
                <span class="transp-nav-icon">📋</span>
                <span>Regulamento</span>
            </a>
            <a href="/transparencia/termos" class="transp-nav-card">
                <span class="transp-nav-icon">🤝</span>
                <span>Termos</span>
            </a>
            <a href="/transparencia/painel-transferencias" class="transp-nav-card">
                <span class="transp-nav-icon">📊</span>
                <span>Painel de Transferências</span>
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
