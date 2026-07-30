<?php
/**
 * Transparência: Declaração — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Declaração de Transparência';
$page_description = 'Declaração pública de transparência e prestação de contas do Instituto Atleta Para Sempre.';

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Declaração</span>
        </nav>
        <h1 class="page-hero-title">Declaração de Transparência</h1>
        <p class="page-hero-sub">Compromisso público com a ética, a boa governança e a gestão responsável dos recursos.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link active">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="declaracao">
    <div class="container container-narrow">
        <div class="transparency-card fade-in-up">
            <div class="transparency-icon">📋</div>
            <h2 class="section-title mb-4">Compromisso com a Transparência</h2>
            <p>O <strong>Instituto Atleta Para Sempre</strong>, no cumprimento de sua missão institucional e em atendimento à legislação vigente, declara publicamente seu compromisso com a transparência e a boa governança na gestão dos recursos públicos e privados que lhe são confiados.</p>
            <p>Em conformidade com a <strong>Lei de Incentivo ao Esporte (Lei nº 11.438/2006)</strong>, a <strong>Lei das MROSC (Lei nº 13.019/2014)</strong> e com a <strong>Lei de Acesso à Informação (Lei nº 12.527/2011)</strong>, disponibilizamos de forma pública e acessível:</p>
            
            <ul class="transparency-list my-6">
                <li>✓ Estatuto Social e alterações consolidadas</li>
                <li>✓ Identificação dos dirigentes, diretores e responsáveis técnicos</li>
                <li>✓ Relatórios financeiros, balanços e prestações de contas</li>
                <li>✓ Editais de seleção de pessoal e fornecedores</li>
                <li>✓ Regulamento próprio de compras e contratações</li>
                <li>✓ Termos de fomento e colaboração celebrados</li>
                <li>✓ Painel de transferências e comprovantes de execução</li>
            </ul>

            <p>Reafirmamos nosso compromisso permanente com a ética, a legalidade e o uso eficiente dos recursos destinados ao fortalecimento do esporte e ao desenvolvimento humano em Pernambuco e no Brasil.</p>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
