<?php
/**
 * Transparência: Declaração — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Declaração de Transparência';
$page_description = 'Declaração pública de transparência e compromisso ético do Instituto Atleta Para Sempre.';

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
    <div class="container">
        <!-- HEADER METRICS BANNER -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">⚖️</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Lei 13.019/2014</span>
                    <span class="s-lbl">Marco Regulatório MROSC</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🏆</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Lei 11.438/2006</span>
                    <span class="s-lbl">Incentivo ao Esporte</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📢</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Lei 12.527/2011</span>
                    <span class="s-lbl">Acesso à Informação</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">💯</div>
                <div class="s-info">
                    <span class="s-num">100%</span>
                    <span class="s-lbl">Acesso Público</span>
                </div>
            </div>
        </div>

        <!-- MAIN DECLARATION BANNER CARD -->
        <div class="transp-banner-card fade-in-up mb-8">
            <div class="t-banner-icon">📋</div>
            <div class="t-banner-content">
                <span class="section-tag">Compromisso Público</span>
                <h2>Gestão Ética, Aberta e Transparente</h2>
                <p>O <strong>Instituto Atleta Para Sempre</strong>, no cumprimento de sua missão institucional e em atendimento à legislação vigente, declara publicamente seu compromisso com a transparência e a boa governança na gestão dos recursos públicos e privados que lhe são confiados.</p>
            </div>
        </div>

        <!-- FEATURE CARDS GRID -->
        <div class="painel-grid fade-in-up">
            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">📜</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">GOVERNANÇA</span>
                    </div>
                    <h3 class="painel-card-title">Estatuto & Normas</h3>
                    <p class="painel-card-desc">Constituição jurídica da entidade, regras societárias, objetivos sociais e competências.</p>
                    <a href="/transparencia/estatuto" class="btn btn-outline btn-sm w-100">Acessar Estatuto →</a>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">👥</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img">DIRETORIA</span>
                    </div>
                    <h3 class="painel-card-title">Corpo Diretivo</h3>
                    <p class="painel-card-desc">Identificação dos diretores, conselheiros fiscais e responsáveis técnicos institucionais.</p>
                    <a href="/transparencia/dirigentes" class="btn btn-outline btn-sm w-100">Ver Dirigentes →</a>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">💰</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">FINANÇAS</span>
                    </div>
                    <h3 class="painel-card-title">Demonstrativos Financeiros</h3>
                    <p class="painel-card-desc">Balanços patrimoniais, prestações de contas e relatórios de auditoria contábil.</p>
                    <a href="/transparencia/financeiro" class="btn btn-outline btn-sm w-100">Ver Financeiro →</a>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">📋</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img">COMPRAS</span>
                    </div>
                    <h3 class="painel-card-title">Regulamento de Compras</h3>
                    <p class="painel-card-desc">Procedimentos próprios para contratação de serviços e aquisição de materiais desportivos.</p>
                    <a href="/transparencia/regulamento" class="btn btn-outline btn-sm w-100">Ver Regulamento →</a>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">🤝</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">PARCERIAS</span>
                    </div>
                    <h3 class="painel-card-title">Termos de Fomento</h3>
                    <p class="painel-card-desc">Acordos, termos de colaboração e parcerias celebradas com o setor público e privado.</p>
                    <a href="/transparencia/termos" class="btn btn-outline btn-sm w-100">Ver Termos →</a>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">📊</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img">TRANSFERÊNCIAS</span>
                    </div>
                    <h3 class="painel-card-title">Painel de Transferências</h3>
                    <p class="painel-card-desc">Demonstrativos visuais e planilhas de comprovantes de transferências legais.</p>
                    <a href="/transparencia/painel" class="btn btn-outline btn-sm w-100">Ver Painel →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
